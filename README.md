# WP Simple Dropshipping

**Version:** 1.0  
**Author:** Raihan  
**License:** GPL-2.0+

## Description

**WP Simple Dropshipping** is a powerful WordPress plugin designed to seamlessly integrate dropshipping functionality into your WooCommerce store. This plugin automates order notifications, allowing you to efficiently manage dropshipping operations directly from your WooCommerce dashboard.

## Features

- **Dropshipping Product Management**
  - Easily mark products as dropshipping items.
  - Automatically set dropshipping metadata during product import.

- **Automated Order Notifications**
  - Sends detailed order emails to your vendor with a customized sender email.
  - Includes dynamic placeholders for comprehensive order details.

- **Customizable Email Templates**
  - Use a rich text editor to design your email templates.
  - Insert dynamic placeholders like `{order_id}`, `{product_name}`, `{sku}`, `{quantity}`, and more.

- **Logo Integration**
  - Add your company logo to email templates via a simple URL field.

- **Custom Sender Email**
  - Set the email sender to `sales@o2cigars.com` for professional correspondence.

## Installation

1. **Download the Plugin**
   - Clone the repository or download the ZIP file from GitHub.

2. **Upload to WordPress**
   - Navigate to your WordPress dashboard.
   - Go to `Plugins > Add New > Upload Plugin`.
   - Select the downloaded ZIP file and click `Install Now`.

3. **Activate the Plugin**
   - After installation, click `Activate Plugin`.

## Configuration

1. **Access Settings**
   - Go to `WooCommerce > Dropshipping Settings` in your WordPress admin dashboard.

2. **Configure Main Settings**
   - **Vendor Email:** Enter the vendor's email address to receive order notifications.
   - **Email Logo URL:** Provide the direct URL to your logo image (e.g., `https://yourwebsite.com/path-to-logo.png`).
   - **Email Subject:** Customize the subject line of the notification email.
   - **Email Template:** Design your email body using the rich text editor. Utilize available placeholders for dynamic content.

3. **Mark Products as Dropshipping**
   - Edit a WooCommerce product.
   - In the **Product Data** section, check the **Dropshipping Product** checkbox to mark it as a dropshipping item.

## Usage

### Marking a Product as Dropshipping

1. **Edit Product**
   - Navigate to `Products > All Products` and select the product you want to mark as dropshipping.

2. **Enable Dropshipping**
   - In the **Product Data** section, locate the **Dropshipping Product** checkbox.
   - Check the box to mark the product as a dropshipping item.
   - Click `Update` to save changes.

### Placing an Order

When a customer places an order containing a dropshipping product:

1. **Order Processing**
   - The plugin automatically detects dropshipping products in the order.

2. **Email Notification**
   - An email is sent to the configured vendor email (`prestige_import_email`) with all relevant order details.

## Settings

### Email Template Placeholders

Customize your email template using the following placeholders to include dynamic content:

- `{order_id}` - Order ID
- `{product_name}` - Product Name
- `{sku}` - Product SKU
- `{quantity}` - Quantity Ordered
- `{shipping_address}` - Shipping Address
- `{billing_email}` - Customer's Billing Email
- `{billing_phone}` - Customer's Billing Phone
- `{order_total}` - Total Order Amount
- `{logo}` - Your Company Logo

**Example Email Template:**

```html
{logo}<br><br>
<strong>New Dropshipping Order Received</strong><br><br>
<strong>Order ID:</strong> {order_id}<br>
<strong>Product:</strong> {product_name}<br>
<strong>SKU:</strong> {sku}<br>
<strong>Quantity:</strong> {quantity}<br>
<strong>Shipping Address:</strong> {shipping_address}<br>
<strong>Billing Email:</strong> {billing_email}<br>
<strong>Billing Phone:</strong> {billing_phone}<br>
<strong>Order Total:</strong> {order_total}<br><br>
Thank you for your order!
