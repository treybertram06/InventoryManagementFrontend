# Remaining work

## Requirements
(gaps between the project overview and the current app)

None currently open - see Done below.

## Optional upgrades
(not called for by the overview, but worth considering)

- Refactor role checks to middleware-style access control - Ryan
  - Cleanup for maintainability, not a functional gap on its own
- Revenue trend reporting - will require lots more test data (ugh)
  - The stats are already computed in `admin_dashboard_data()`, but the trend view will look thin until there's more realistic test data

Done:
- Allow technicians to process sales, not just admins
  - `deviceSale.php` now uses `require_login()`; the Sell link in `itemTable.view.php` renders for any logged-in user
- Require login on device intake
  - `intake.php` now calls `require_login()` before handling GET/POST
- Support backdated sales (admin only)
  - Sale form has an optional Sale Date & Time field, shown only to admins; validated server-side (must parse, can't be in the future) and non-admin submissions of the field are ignored
- Admin editing of sales records beyond deletion / reversal
  - New `/admin-sale-edit` route + `adminSaleEdit.php` controller let admins edit price, channel, buyer info, sold-at, and notes on a non-reversed sale
- More thorough inventory filters
  - Added Model, Storage, and Battery Health (min threshold) filters to the inventory table alongside the existing Grade dropdown + search
- Add a sales processing module - Viktoria
  - Brought there by selecting a device
  - Has a form to input necessary sales info
- Admin dashboard
  - User management
  - Manual data editing
  - Sale reversal
- Home page
  - Update to display real-time data once everything else is done
  - Sale reversal
- Sales history and reporting page
  - Sales statistics and whatnot