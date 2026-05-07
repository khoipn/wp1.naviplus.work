=== MailerPress - Send Beautiful Email Campaigns ===
Authors: mailerpress
Contributors: mailerpress, seopress, rainbowgeek, maigret
Donate link: https://mailerpress.com/
Tags: newsletter, emailing, email marketing, mjml, automation
Requires at least: 6.5
Tested up to: 6.9
Stable tag: 1.5.1
Requires PHP: 8.2
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Transform your WordPress site into a powerful email marketing platform with MailerPress - the most comprehensive and user-friendly email solution.

== Description ==

MailerPress revolutionizes email marketing for WordPress users by combining powerful functionality with intuitive design. Whether you're a small business owner, blogger, or enterprise marketer, MailerPress provides everything you need to create, manage, and optimize professional email campaigns directly from your WordPress dashboard.

**Key Capabilities:**
• Create stunning email campaigns with our advanced drag-and-drop editor
• Manage unlimited contacts, lists, and campaigns
• Automate your email marketing with intelligent campaign scheduling
• Design beautiful templates with our modern, Block Editor-inspired interface
• Track performance and optimize your campaigns for maximum engagement
• Seamlessly integrate with your favorite WordPress plugins and themes

[youtube https://youtu.be/dDq0v-wdSUk]

== Why Choose MailerPress? ==

**💰 Cost-Effective Solution**
Start completely free with unlimited contacts and campaigns. Our free version provides everything you need to launch successful email marketing campaigns. Upgrade to Pro for advanced features including premium templates, professional email delivery services, AI-powered content optimization, and premium integrations.

**⚡ Save Time & Boost Productivity**
Create professional emails in seconds with our premium template library. Import contacts effortlessly via CSV files. Leverage AI technology to enhance your content, fix grammar, adjust length, and generate compelling visuals. Set up automated campaigns to keep your audience engaged with your latest content.

**🎯 No-Code Required**
Built for everyone - no technical expertise needed. Our intuitive interface operates entirely within your WordPress admin panel. The modern editor, inspired by the WordPress Block Editor, ensures you'll never feel lost while creating sophisticated email campaigns.

**🚀 Unlimited Everything**
Enjoy unlimited contacts, campaigns, lists, and tags - even in the free version. Scale your email marketing without restrictions or hidden limitations.

== Free Features ==

**Email Creation & Design**
• Advanced drag-and-drop email editor
• Comprehensive template management system
• Save and reuse custom layouts
• Full Site Editing (FSE) compatibility with theme.json
• Google Fonts integration with local hosting
• MJML template import for professional designs

**Contact Management**
• Unlimited contacts, lists, and tags
• CSV import/export functionality
• Advanced contact segmentation
• GDPR-compliant subscription management
• Fully customizable opt-in form shortcode to capture contacts—seamless with all popular page builders
• Double opt-in with customizable confirmation emails
• Merge tags for personalized content

**Campaign Management**
• Professional campaign creation and scheduling
• Content retrieval from posts, pages, and products
• WooCommerce integration for e-commerce campaigns
• Multiple sending options (PHP Mail, custom SMTP)
• Comprehensive campaign analytics

**Privacy & Compliance**
• GDPR-friendly with built-in unsubscribe functionality
• Complete data privacy - no external data collection
• Self-hosted solution for maximum security

<a href="https://mailerpress.com/features">Explore all MailerPress features</a>

== Pro Features ==
**Professional Email Delivery**
• Premium email service integrations (SendGrid, Brevo, MailJet, Amazon SES, Mailgun, Postmark and more)
• Enhanced deliverability and reputation management
• Implemented bounce management using email provider APIs
• Advanced analytics and reporting

**AI-Powered Marketing**
• Artificial Intelligence integration (OpenAI, DeepSeek, Mistral, Google Gemini)
• Automated content optimization
• Smart image generation and enhancement

**Advanced Integrations**
• Premium form plugin integrations (Contact Form 7, Gravity Forms, Fluent Forms and more)
• Premium page builder plugin integrations (Elementor, Bricks and more)
• Advanced automation workflows
• Contact segmentation and behavioral targeting (custom fields and segments)

**Professional Tools**
• Easily embed opt-in forms on any external website
• Premium template library
• Mobile campaign management
• White-label customization options
• Automated follow-up emails for pending double opt-in confirmations
• Priority customer support

<a href="https://mailerpress.com/">Upgrade to MailerPress Pro</a>

== Developer-Friendly ==

MailerPress is built with developers in mind, offering extensive customization options:

• **Hundreds of hooks** for seamless plugin behavior modification
• **REST API** for third-party integrations and custom applications
• **MJML template support** for advanced email design workflows
• **React-based architecture** for maximum performance and extensibility

== Privacy & Security ==

**Privacy by Design**
MailerPress is fully GDPR-compliant and privacy-focused. We collect no data, ensuring your information remains secure on your own server. When using external email providers, please review their respective privacy policies for detailed data handling information.

== Community & Support ==

**Translation Support**
Help expand MailerPress globally by <a href="https://translate.wordpress.org/projects/wp-plugins/mailerpress/">contributing translations</a>.
Languages currently available: English, French, Spanish, Dutch, and Arabic. Additional languages are being added regularly.

**Customer Support**
• **Free users**: Access community support through our <a href="https://wordpress.org/support/plugin/mailerpress/">WordPress.org forum</a>
• **Pro users**: Receive priority support via our <a href="https://mailerpress.com/support/">dedicated customer portal</a>

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/mailerpress` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' screen in WordPress
1. Use MailerPress screen to get started

== Frequently Asked Questions ==

= How do I get support? =

For Free users: <a href="https://wordpress.org/support/plugin/mailerpress/">Forums</a>
For Pro users: support by mail from their customer account.

== Screenshots ==
1. MailerPress dashboard
2. Customize MailerPress dashboard
3. Wizard to create a new email campaign
4. The MailerPress block editor to create your campaigns
5. Available blocks to build your emails
6. Wizard assistant to quickly get started with MailerPress
7. Wizard assistant to quickly get started with MailerPress
8. Wizard assistant to quickly get started with MailerPress
9. Wizard assistant to quickly get started with MailerPress
10. Wizard assistant to quickly get started with MailerPress
11. Wizard assistant to quickly get started with MailerPress
12. Email Serice Providers integrations
13. Third-party extensions integrations
14. Incoming webhooks
15. Outgoing webhooks

== Changelog ==
= 1.5.1 =
* FIX: Campaign scheduled date now uses WordPress locale formatting (correct date order and translation of "at").
* FIX: Preserved spacing before punctuation (e.g. " !") in email button text.
* FIX: Bulk actions on contacts now correctly apply to all filtered records instead of only the current page.
* FIX: Default contact list can no longer trigger the edit action; a notice now explains that the default list is protected.
* FIX: Double opt-in confirmation was not triggered for subscriptions created via shortcode forms.
* FIX: Honeypot anti-spam check not working properly with Contact Form 7 integration.
* SECURITY: Hardened various plugin endpoints and improved overall input validation and sanitization.

= 1.5.0 <a href="https://mailerpress.com/mailerpress-1-5/">Read the blog post update</a> =
* NEW: Webhooks: Support for both incoming and outgoing webhooks to integrate external systems and receive real-time events.
* NEW: Flexible click and open tracking: Choose between Yes, No, or Anonymously in the Review & Send modal.
* IMPROVEMENT: DataView Added support for ordering by columns.
* IMPROVEMENT: Embed form, you can now customize all texts that were previously rendered in English only.
* FIX: DataView fixed an issue where column display settings were not saved when navigating away.
* FIX: Editor issues where unsaved changes were not correctly detected in some cases.
* FIX: MailerPress option form (Gutenberg block): Fixed border radius settings so they now apply correctly to both the submit button and form fields.
* SECURITY: Strengthened security around internal network requests.

<a href="https://mailerpress.com/docs/mailerpress-changelog/" target="_blank">View our complete changelog</a>
