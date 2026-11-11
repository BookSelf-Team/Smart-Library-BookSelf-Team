<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class FileUploadSanitizer
{
    /**
     * MIME types allowed for images and their canonical safe extensions.
     *
     * @var array<string, array<string>>
     */
    protected const ALLOWED_IMAGE_MIMES = [
        'image/jpeg' => ['jpg', 'jpeg'],
        'image/png' => ['png'],
        'image/webp' => ['webp'],
        'image/gif' => ['gif'],
    ];

    /**
     * MIME types allowed for documents.
     *
     * @var array<string, array<string>>
     */
    protected const ALLOWED_DOCUMENT_MIMES = [
        'application/pdf' => ['pdf'],
    ];

    /**
     * Maximum default file size in kilobytes (2MB).
     */
    protected const DEFAULT_MAX_SIZE_KB = 2048;

    /**
     * Dangerous executable extensions that must never be permitted under any condition.
     *
     * @var array<string>
     */
    protected const DANGEROUS_EXTENSIONS = [
        'php', 'php3', 'php4', 'php5', 'phtml', 'phar', 'exe', 'bin', 'bat', 'cmd',
        'sh', 'bash', 'js', 'jar', 'vbs', 'scr', 'msi', 'com', 'cgi', 'pl', 'py',
        'html', 'htm', 'xhtml', 'shtml', 'svg', 'xml',
    ];

    /**
     * Validates, sanitizes, and securely stores an uploaded image file.
     *
     * @throws ValidationException
     */
    public function sanitizeAndStoreImage(
        UploadedFile $file,
        string $directory = 'images',
        string $disk = 'public',
        int $maxSizeKb = self::DEFAULT_MAX_SIZE_KB,
    ): string {
        $this->validateFile($file, self::ALLOWED_IMAGE_MIMES, $maxSizeKb);
        $this->ensureNoExecutablePayload($file);

        $safeExtension = $this->determineCanonicalExtension($file, self::ALLOWED_IMAGE_MIMES);
        $safeFileName = Str::random(40).'.'.$safeExtension;

        return $file->storeAs($directory, $safeFileName, $disk);
    }

    /**
     * Validates, sanitizes, and securely stores an uploaded document or image.
     *
     * @param  array<string, array<string>>|null  $allowedMimeMap
     *
     * @throws ValidationException
     */
    public function sanitizeAndStore(
        UploadedFile $file,
        string $directory = 'uploads',
        string $disk = 'public',
        ?array $allowedMimeMap = null,
        int $maxSizeKb = self::DEFAULT_MAX_SIZE_KB,
    ): string {
        $allowed = $allowedMimeMap ?? (self::ALLOWED_IMAGE_MIMES + self::ALLOWED_DOCUMENT_MIMES);

        $this->validateFile($file, $allowed, $maxSizeKb);
        $this->ensureNoExecutablePayload($file);

        $safeExtension = $this->determineCanonicalExtension($file, $allowed);
        $safeFileName = Str::random(40).'.'.$safeExtension;

        return $file->storeAs($directory, $safeFileName, $disk);
    }

    /**
     * Sanitize a client filename for safe logging or presentation, removing path traversal and unsafe characters.
     */
    public function sanitizeOriginalFilename(string $filename): string
    {
        // Remove null bytes and path traversal sequences
        $clean = str_replace(["\0", '../', '..\\', './', '.\\'], '', $filename);

        // Extract base filename without path
        $basename = basename(str_replace('\\', '/', $clean));

        // Transliterate and strip characters outside of safe alphanumeric, dash, dot, and underscore
        $sanitized = preg_replace('/[^a-zA-Z0-9._-]/', '_', $basename);

        return $sanitized ?: 'file_'.Str::random(8);
    }

    /**
     * Validate the uploaded file integrity, real MIME type, and size.
     *
     * @param  array<string, array<string>>  $allowedMimes
     *
     * @throws ValidationException
     */
    public function validateFile(UploadedFile $file, array $allowedMimes, int $maxSizeKb): void
    {
        if (! $file->isValid()) {
            throw ValidationException::withMessages([
                'file' => 'Berkas upload gagal atau rusak.',
            ]);
        }

        // Size check (size is in bytes)
        if ($file->getSize() > ($maxSizeKb * 1024)) {
            throw ValidationException::withMessages([
                'file' => "Ukuran berkas melebihi batas maksimum {$maxSizeKb} KB.",
            ]);
        }

        // Check user extension against forbidden blacklist
        $rawExt = strtolower((string) pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION));
        if (in_array($rawExt, self::DANGEROUS_EXTENSIONS, true)) {
            throw ValidationException::withMessages([
                'file' => 'Tipe berkas ini diblokir demi alasan keamanan.',
            ]);
        }

        // Inspect real content MIME type using fileinfo
        $realMime = $this->detectRealMimeType($file);
        if (! array_key_exists($realMime, $allowedMimes)) {
            throw ValidationException::withMessages([
                'file' => "Format berkas tidak diizinkan. Tipe terdeteksi: {$realMime}.",
            ]);
        }

        // Ensure user-supplied extension matches allowed extensions for this MIME
        $clientExt = strtolower((string) $file->getClientOriginalExtension());
        $allowedExtensions = $allowedMimes[$realMime];
        if (! in_array($clientExt, $allowedExtensions, true) && ! in_array($rawExt, $allowedExtensions, true)) {
            throw ValidationException::withMessages([
                'file' => 'Ekstensi berkas tidak sesuai dengan tipe konten berkas.',
            ]);
        }
    }

    /**
     * Inspect file content for malicious web shell or executable signatures.
     *
     * @throws ValidationException
     */
    public function ensureNoExecutablePayload(UploadedFile $file): void
    {
        $path = $file->getRealPath();
        if (! $path || ! file_exists($path)) {
            throw ValidationException::withMessages([
                'file' => 'Berkas sementara tidak dapat dibaca.',
            ]);
        }

        // Read sample content (first 8KB and last 8KB) to inspect for embedded PHP tags
        $handle = @fopen($path, 'rb');
        if (! $handle) {
            return;
        }

        $header = (string) fread($handle, 8192);
        $fileSize = filesize($path) ?: 0;
        $footer = '';
        if ($fileSize > 8192) {
            fseek($handle, max(0, $fileSize - 8192));
            $footer = (string) fread($handle, 8192);
        }
        fclose($handle);

        $contentSample = strtolower($header.' '.$footer);

        $suspiciousSignatures = [
            '<?php',
            '<?=',
            '<script',
            'eval(',
            'base64_decode(',
            'system(',
            'shell_exec(',
            'passthru(',
            '__halt_compiler()',
        ];

        foreach ($suspiciousSignatures as $sig) {
            if (str_contains($contentSample, $sig)) {
                throw ValidationException::withMessages([
                    'file' => 'Berkas mengandung konten yang mencurigakan dan ditolak.',
                ]);
            }
        }
    }

    /**
     * Detect real MIME type of the file on disk using PHP finfo.
     */
    public function detectRealMimeType(UploadedFile $file): string
    {
        $path = $file->getRealPath();
        if ($path && file_exists($path)) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            if ($finfo) {
                $mime = finfo_file($finfo, $path);
                finfo_close($finfo);
                if ($mime && is_string($mime)) {
                    return strtolower($mime);
                }
            }
        }

        return strtolower((string) $file->getMimeType());
    }

    /**
     * Determine canonical safe extension from verified MIME mapping.
     *
     * @param  array<string, array<string>>  $allowedMimes
     */
    public function determineCanonicalExtension(UploadedFile $file, array $allowedMimes): string
    {
        $realMime = $this->detectRealMimeType($file);

        return $allowedMimes[$realMime][0] ?? 'bin';
    }
}
