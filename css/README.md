# CSS Structure Documentation

## Overview
CSS telah dipisahkan menjadi beberapa file berdasarkan fungsinya untuk memudahkan maintenance dan pengembangan.

## File Structure

```
css/
├── base.css          # Reset, variables, utility classes
├── navigation.css    # Navbar, header, mobile menu
├── hero.css          # Hero section, features
├── products.css      # Products grid, filters, cards
├── markets.css       # Markets section, market cards
├── forms.css         # Forms, login, admin dashboard
├── footer.css        # Footer, about, contact
├── modal.css         # Modal, animations, loading states
└── README.md         # This file
```

## File Descriptions

### `base.css`
- CSS reset dan normalize
- CSS variables (custom properties)
- Base typography
- Utility classes
- Button styles

### `navigation.css`
- Navbar styling
- Search functionality
- Mobile menu
- User menu
- Responsive navigation

### `hero.css`
- Hero section styling
- Features section
- Call-to-action buttons
- Responsive hero layout

### `products.css`
- Product cards
- Price filters
- Product grid layout
- Search results
- No data states

### `markets.css`
- Market cards
- Market information
- Market statistics
- Market links

### `forms.css`
- Form styling
- Login page
- Admin dashboard
- Table styles
- Status messages

### `footer.css`
- Footer layout
- About section
- Contact section
- Social links
- App buttons

### `modal.css`
- Modal dialogs
- Animations
- Loading states
- Scrollbar styling

## Usage

Semua file CSS diimpor melalui `style.css` utama:

```css
@import url('css/base.css');
@import url('css/navigation.css');
@import url('css/hero.css');
@import url('css/products.css');
@import url('css/markets.css');
@import url('css/forms.css');
@import url('css/footer.css');
@import url('css/modal.css');
```

## Benefits

✅ **Modularity**: Setiap komponen memiliki file terpisah
✅ **Maintainability**: Mudah menemukan dan mengubah style tertentu
✅ **Team Collaboration**: Tim bisa bekerja parallel tanpa konflik
✅ **Performance**: File lebih kecil, loading lebih cepat
✅ **Reusability**: Komponen bisa digunakan ulang
✅ **Scalability**: Mudah menambah komponen baru

## CSS Variables

Semua warna dan spacing menggunakan CSS variables di `base.css`:

```css
:root {
    --primary-color: #92C7CF;
    --secondary-color: #AAD7D9;
    --light-bg: #FBF9F1;
    --neutral-color: #E5E1DA;
    --text-dark: #2c3e50;
    --text-light: #7f8c8d;
    --white: #ffffff;
    --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    --shadow-hover: 0 8px 15px rgba(0, 0, 0, 0.15);
    --transition: all 0.3s ease;
}
```

## Responsive Design

Semua file CSS sudah responsive dengan breakpoints:
- Desktop: > 992px
- Tablet: 768px - 992px  
- Mobile: < 768px
- Small Mobile: < 480px 