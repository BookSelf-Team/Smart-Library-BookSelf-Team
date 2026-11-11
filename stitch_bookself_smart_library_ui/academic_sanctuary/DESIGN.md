---
name: Academic Sanctuary
colors:
  surface: '#fcf9ef'
  surface-dim: '#dddad0'
  surface-bright: '#fcf9ef'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f6f4ea'
  surface-container: '#f1eee4'
  surface-container-high: '#ebe8de'
  surface-container-highest: '#e5e2d9'
  on-surface: '#1c1c16'
  on-surface-variant: '#45483c'
  inverse-surface: '#31312a'
  inverse-on-surface: '#f4f1e7'
  outline: '#75796b'
  outline-variant: '#c5c8b8'
  surface-tint: '#50652a'
  primary: '#3e5219'
  on-primary: '#ffffff'
  primary-container: '#556b2f'
  on-primary-container: '#d0eba1'
  inverse-primary: '#b6d088'
  secondary: '#576244'
  on-secondary: '#ffffff'
  secondary-container: '#dbe8c0'
  on-secondary-container: '#5d6849'
  tertiary: '#425223'
  on-tertiary: '#ffffff'
  tertiary-container: '#596a38'
  on-tertiary-container: '#d6e9ab'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#d2eca2'
  primary-fixed-dim: '#b6d088'
  on-primary-fixed: '#131f00'
  on-primary-fixed-variant: '#394d14'
  secondary-fixed: '#dbe8c0'
  secondary-fixed-dim: '#bfcba6'
  on-secondary-fixed: '#151e06'
  on-secondary-fixed-variant: '#404b2e'
  tertiary-fixed: '#d7eaac'
  tertiary-fixed-dim: '#bbce92'
  on-tertiary-fixed: '#141f00'
  on-tertiary-fixed-variant: '#3d4c1e'
  background: '#fcf9ef'
  on-background: '#1c1c16'
  surface-variant: '#e5e2d9'
typography:
  display-lg:
    fontFamily: Newsreader
    fontSize: 56px
    fontWeight: '500'
    lineHeight: 64px
    letterSpacing: -0.02em
  display-lg-mobile:
    fontFamily: Newsreader
    fontSize: 38px
    fontWeight: '500'
    lineHeight: 44px
    letterSpacing: -0.01em
  headline-lg:
    fontFamily: Newsreader
    fontSize: 36px
    fontWeight: '600'
    lineHeight: 44px
    letterSpacing: -0.015em
  headline-lg-mobile:
    fontFamily: Newsreader
    fontSize: 28px
    fontWeight: '600'
    lineHeight: 34px
    letterSpacing: -0.01em
  headline-md:
    fontFamily: Newsreader
    fontSize: 28px
    fontWeight: '600'
    lineHeight: 36px
    letterSpacing: -0.01em
  headline-sm:
    fontFamily: Newsreader
    fontSize: 22px
    fontWeight: '600'
    lineHeight: 30px
  title-lg:
    fontFamily: Plus Jakarta Sans
    fontSize: 20px
    fontWeight: '600'
    lineHeight: 28px
    letterSpacing: -0.01em
  title-md:
    fontFamily: Plus Jakarta Sans
    fontSize: 16px
    fontWeight: '600'
    lineHeight: 24px
  title-sm:
    fontFamily: Plus Jakarta Sans
    fontSize: 14px
    fontWeight: '600'
    lineHeight: 20px
  body-lg:
    fontFamily: Plus Jakarta Sans
    fontSize: 16px
    fontWeight: '400'
    lineHeight: 26px
  body-md:
    fontFamily: Plus Jakarta Sans
    fontSize: 14px
    fontWeight: '400'
    lineHeight: 22px
  body-sm:
    fontFamily: Plus Jakarta Sans
    fontSize: 12px
    fontWeight: '400'
    lineHeight: 18px
  label-lg:
    fontFamily: Plus Jakarta Sans
    fontSize: 14px
    fontWeight: '500'
    lineHeight: 20px
    letterSpacing: 0.01em
  label-md:
    fontFamily: Plus Jakarta Sans
    fontSize: 12px
    fontWeight: '500'
    lineHeight: 16px
    letterSpacing: 0.02em
  label-sm:
    fontFamily: Plus Jakarta Sans
    fontSize: 11px
    fontWeight: '600'
    lineHeight: 14px
    letterSpacing: 0.04em
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  gutter: 1.5rem
  gutter-mobile: 0.75rem
  margin: 2.5rem
  margin-mobile: 1rem
  space-xs: 0.25rem
  space-sm: 0.5rem
  space-md: 1rem
  space-lg: 1.5rem
  space-xl: 2.5rem
  space-2xl: 4rem
---

## Brand & Style

This design system embodies the quiet intellect, dignity, and calm focus of a contemporary scholarly institution merged with the speed of an intelligent cataloging platform. Designed for academic librarians, researchers, university staff, and discerning readers, the experience evokes trust, archival permanence, and effortless curation.

The aesthetic blends **Modern Editorial** with **Tactile Minimalist warmth**:
- **Literary Prestige**: Drawing inspiration from leather bindings, archival rag paper, and heritage reading rooms without feeling outdated or dusty.
- **Academic Clarity**: Clean geometric utility in UI controls, allowing book spines, metadata, and research logs to breathe without digital fatigue.
- **Warm Groundedness**: Replacing sterile software grays with rich ivory creams and botanical olive tones that reduce optical glare during prolonged cataloging sessions.

## Colors

The palette derives from the heritage botanical bindings of private collegiate collections:

- **Primary (`#556B2F`)**: Dark Olive Green. Serves as the primary brand anchor for key call-to-actions, active navigation highlights, primary tags, and focal status indicators.
- **Secondary (`#232D13` / `#38461E`)**: Deep Forest Olive. Acts as high-contrast typographic ink, deep navigation headers, and authoritative interactive states.
- **Tertiary (`#8B9D66`)**: Soft Sage. Employed for secondary chips, badge fills, subtle status indicators, and focus ring accents.
- **Neutral Canvas (`#FBF8EE` / `#FFFDD0`)**: Warm Cream / Ivory. Serves as the page substrate and default background layer, cutting harsh blue-white glare and providing the tactile presence of archival paper.
- **Surface Elevation Containers**:
  - `surface-base`: `#FBF8EE` (Root canvas)
  - `surface-container-low`: `#F5F1E4` (Sidebar, table footers)
  - `surface-container`: `#EEE9D8` (Cards, dialog cards, input fills)
  - `surface-container-high`: `#E5DFC9` (Hover states, nested blockquotes, shelf dividers)
- **Borders & Dividers**: Low-contrast olive wash (`rgba(56, 70, 30, 0.12)`).

## Typography

The type system achieves balance through intentional pairing:

- **Display & Headline Hierarchy (Newsreader)**: Evokes the academic prestige of classical typesetting, literary journals, and scholarly volumes. Features subtle italic variations for book titles, catalog quotes, and archival classifications.
- **Body & Interface Hierarchy (Plus Jakarta Sans)**: Ensures maximum legibility for high-density cataloging, ISBN tables, barcode data, and reader records. Its contemporary geometric curves soften functional interactions.
- **Classification & Metatags**: Use `label-sm` in uppercase with subtle letter-spacing for Dewey Decimal or Library of Congress call numbers.

## Layout & Spacing

The structural layout uses an asymmetric fluid grid inspired by academic publications:

- **Desktop (1200px+)**: 12-column layout with fixed navigation rail (`280px`), `2.5rem` outer margins, and `1.5rem` gutters. Content blocks prefer wide margins that simulate generous book leaves.
- **Tablet (768px - 1199px)**: 8-column layout with `1.5rem` outer margin and `1rem` gutters. The primary navigation rail collapses into an off-canvas drawer.
- **Mobile (Up to 767px)**: 4-column layout with `1rem` outer margin and `0.75rem` gutters. Card grids reflow to single-column vertical stacks.
- **Density Guidelines**: Catalog dashboards prioritize information clarity with compact line-level vertical padding (`space-sm`), while public-facing book detail pages expand to editorial rhythm using `space-xl` and `space-2xl`.

## Elevation & Depth

Visual hierarchy relies on **tonal paper layering** complemented by warm, diffused ambient shadows rather than synthetic gray drop-shadows:

- **Level 0 (Base Canvas)**: Warm Ivory (`#FBF8EE`). Flat, grounding the entire interface.
- **Level 1 (Card & Shelf Surfaces)**: Cream layer (`#EEE9D8`) reinforced with a hairline border (`1px solid rgba(56, 70, 30, 0.08)`) and an ultra-soft ambient shadow: `0 2px 8px -2px rgba(35, 45, 19, 0.06)`.
- **Level 2 (Popovers & Filter Flyouts)**: Lifted container (`#FFFDD0`) with `0 8px 24px -4px rgba(35, 45, 19, 0.10)` and `1px solid rgba(56, 70, 30, 0.12)`.
- **Level 3 (Modal Dialogs & Reader Overlays)**: Maximum focus depth featuring `0 16px 40px -8px rgba(35, 45, 19, 0.16)`. Accompanied by a deep forest backdrop veil (`rgba(35, 45, 19, 0.40)`) with a `4px` blur filter.

## Shapes

The shape architecture mirrors book jackets, modern library furniture, and tactile index cards:

- **Standard Elements (Buttons, Inputs, Badges)**: Governed by `roundedness: 2` (base `8px / 0.5rem`).
- **Cards & Reading Panels**: Utilize `rounded-lg` (`16px / 1rem`) to provide an inviting, comfortable frame around book covers and circulation data.
- **Modals & Master Detail Containers**: Utilize `rounded-xl` (`24px / 1.5rem`).
- **Pills**: Reserved strictly for category chips, tag pills, and reading status labels (`rounded-full`).

## Components

### Buttons
- **Primary**: Solid Dark Olive Green (`#556B2F`), text in Warm Cream (`#FFFDD0`). Hover transitions to Deep Forest Olive (`#38461E`) with subtle `translateY(-1px)`. Height: 44px (md), 36px (sm). Radius: `0.5rem`.
- **Secondary / Outlined**: Transparent fill with `1.5px solid #556B2F`, text in `#38461E`. Hover background: `rgba(85, 107, 47, 0.08)`.
- **Tertiary / Text**: No border, text `#556B2F`. Hover underlines with an editorial serif touch.

### Input Fields & Search Bars
- Background set to `#EEE9D8` in rest state, transitioning to `#FFFDD0` on active focus.
- Border: `1px solid rgba(56, 70, 30, 0.20)`. Focus ring: `2px solid #556B2F` with a soft `2px` offset.
- Global catalog search features integrated keyboard shortcut indicators (`⌘K`) in muted sage and book icon adornments.

### Chips & Filter Tags
- **Filter Chip (Inactive)**: Background `#E5DFC9`, text `#38461E`, rounded `9999px`, padding `6px 14px`.
- **Filter Chip (Active)**: Background `#556B2F`, text `#FFFDD0`, accompanied by a subtle checkmark icon.

### Cards (Book Shelf & Record Cards)
- Background `#EEE9D8` with `1px solid rgba(56, 70, 30, 0.08)`.
- Features an aspect-ratio container for book jacket rendering with a `4px` corner radius, spine shadow effect (`inset 3px 0 6px rgba(0,0,0,0.18)`), and bottom metadata cluster (title in `Newsreader`, call number in `Plus Jakarta Sans` monospace weight).

### Lists & Inventory Tables
- Alternating row styling using transparent and `rgba(85, 107, 47, 0.03)` bands.
- Cell dividers: `1px solid rgba(56, 70, 30, 0.06)`. Hover row activates `#F5F1E4`.

### Checkboxes & Radio Buttons
- Base border `1.5px solid #556B2F` on cream ground.
- Checked state: Fill `#556B2F` with `#FFFDD0` check icon. Radio buttons use a centered `#FFFDD0` circular pip inside a `#556B2F` filled ring.

### Dedicated Library Components
- **Call-Number Badge**: A distinct monospaced pill framed in `#38461E` on a `#F5F1E4` pill surface, simulating embossed library label tape.
- **Availability Stamp**: Soft pill indicators for "Available" (Soft Sage `#8B9D66` tint with `#232D13` text), "On Loan" (Muted Ochre tint), and "Archived / Restricted" (Dusty Rust tint).