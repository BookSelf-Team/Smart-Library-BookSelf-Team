<?php

namespace Tests\Unit;

use App\Models\User;
use PHPUnit\Framework\TestCase;

class UserInitialsTest extends TestCase
{
    public function test_initials_are_derived_from_the_first_two_name_words(): void
    {
        $this->assertSame('DA', (new User(['name' => 'Dr. Aris Pratama']))->initials());
        $this->assertSame('RM', (new User(['name' => 'Rani Melati']))->initials());
        $this->assertSame('N', (new User(['name' => 'Nurul']))->initials());
    }

    public function test_initials_fall_back_when_the_name_is_blank(): void
    {
        $this->assertSame('?', (new User(['name' => '   ']))->initials());
    }
}
