<?php
// Language: English is the default. Switch with ?lang=ar or ?lang=en (remembered in a cookie).
// Languages: the public site has English + ONE second language chosen in Admin > Shop info (Arabic, Bengali or Hindi).
// The admin panel itself is English / Arabic. Texts you type in the "second language" boxes are stored in the *_ar columns.
// code => [native label, English name, Arabic name, direction, locale, Google font spec (script fonts for the public page)]
const LANGS = [
  'en' => ['label' => 'EN', 'en' => 'English', 'ar' => 'الإنجليزية', 'dir' => 'ltr', 'loc' => 'en_US', 'font' => ''],
  'ar' => ['label' => 'عربي', 'en' => 'Arabic', 'ar' => 'عربي', 'dir' => 'rtl', 'loc' => 'ar_AR', 'font' => ''],
  'bn' => ['label' => 'বাংলা', 'en' => 'Bengali', 'ar' => 'بنغالي', 'dir' => 'ltr', 'loc' => 'bn_BD', 'font' => 'Noto+Sans+Bengali:wght@400;700;800'],
  'hi' => ['label' => 'हिन्दी', 'en' => 'Hindi', 'ar' => 'هندي', 'dir' => 'ltr', 'loc' => 'hi_IN', 'font' => 'Noto+Sans+Devanagari:wght@400;700;800'],
  'ur' => ['label' => 'اردو', 'en' => 'Urdu', 'ar' => 'أردو', 'dir' => 'rtl', 'loc' => 'ur_PK', 'font' => 'Noto+Naskh+Arabic:wght@400;700'],
  'fa' => ['label' => 'فارسی', 'en' => 'Persian', 'ar' => 'فارسي', 'dir' => 'rtl', 'loc' => 'fa_IR', 'font' => 'Noto+Naskh+Arabic:wght@400;700'],
  'ps' => ['label' => 'پښتو', 'en' => 'Pashto', 'ar' => 'بشتو', 'dir' => 'rtl', 'loc' => 'ps_AF', 'font' => 'Noto+Naskh+Arabic:wght@400;700'],
  'he' => ['label' => 'עברית', 'en' => 'Hebrew', 'ar' => 'عبري', 'dir' => 'rtl', 'loc' => 'he_IL', 'font' => 'Noto+Sans+Hebrew:wght@400;700;800'],
  'tr' => ['label' => 'Türkçe', 'en' => 'Turkish', 'ar' => 'تركي', 'dir' => 'ltr', 'loc' => 'tr_TR', 'font' => 'Noto+Sans:wght@400;700;800'],
  'fr' => ['label' => 'Français', 'en' => 'French', 'ar' => 'فرنسي', 'dir' => 'ltr', 'loc' => 'fr_FR', 'font' => 'Noto+Sans:wght@400;700;800'],
  'es' => ['label' => 'Español', 'en' => 'Spanish', 'ar' => 'إسباني', 'dir' => 'ltr', 'loc' => 'es_ES', 'font' => 'Noto+Sans:wght@400;700;800'],
  'de' => ['label' => 'Deutsch', 'en' => 'German', 'ar' => 'ألماني', 'dir' => 'ltr', 'loc' => 'de_DE', 'font' => 'Noto+Sans:wght@400;700;800'],
  'it' => ['label' => 'Italiano', 'en' => 'Italian', 'ar' => 'إيطالي', 'dir' => 'ltr', 'loc' => 'it_IT', 'font' => 'Noto+Sans:wght@400;700;800'],
  'pt' => ['label' => 'Português', 'en' => 'Portuguese', 'ar' => 'برتغالي', 'dir' => 'ltr', 'loc' => 'pt_PT', 'font' => 'Noto+Sans:wght@400;700;800'],
  'ru' => ['label' => 'Русский', 'en' => 'Russian', 'ar' => 'روسي', 'dir' => 'ltr', 'loc' => 'ru_RU', 'font' => 'Noto+Sans:wght@400;700;800'],
  'id' => ['label' => 'Indonesia', 'en' => 'Indonesian', 'ar' => 'إندونيسي', 'dir' => 'ltr', 'loc' => 'id_ID', 'font' => 'Noto+Sans:wght@400;700;800'],
  'ms' => ['label' => 'Melayu', 'en' => 'Malay', 'ar' => 'ماليزي', 'dir' => 'ltr', 'loc' => 'ms_MY', 'font' => 'Noto+Sans:wght@400;700;800'],
  'tl' => ['label' => 'Filipino', 'en' => 'Filipino', 'ar' => 'فلبيني', 'dir' => 'ltr', 'loc' => 'fil_PH', 'font' => 'Noto+Sans:wght@400;700;800'],
  'vi' => ['label' => 'Tiếng Việt', 'en' => 'Vietnamese', 'ar' => 'فيتنامي', 'dir' => 'ltr', 'loc' => 'vi_VN', 'font' => 'Noto+Sans:wght@400;700;800'],
  'th' => ['label' => 'ไทย', 'en' => 'Thai', 'ar' => 'تايلاندي', 'dir' => 'ltr', 'loc' => 'th_TH', 'font' => 'Noto+Sans+Thai:wght@400;700;800'],
  'zh' => ['label' => '中文', 'en' => 'Chinese', 'ar' => 'صيني', 'dir' => 'ltr', 'loc' => 'zh_CN', 'font' => 'Noto+Sans+SC:wght@400;700;800'],
  'ja' => ['label' => '日本語', 'en' => 'Japanese', 'ar' => 'ياباني', 'dir' => 'ltr', 'loc' => 'ja_JP', 'font' => 'Noto+Sans+JP:wght@400;700;800'],
  'ko' => ['label' => '한국어', 'en' => 'Korean', 'ar' => 'كوري', 'dir' => 'ltr', 'loc' => 'ko_KR', 'font' => 'Noto+Sans+KR:wght@400;700;800'],
  'ta' => ['label' => 'தமிழ்', 'en' => 'Tamil', 'ar' => 'تاميلي', 'dir' => 'ltr', 'loc' => 'ta_IN', 'font' => 'Noto+Sans+Tamil:wght@400;700;800'],
  'te' => ['label' => 'తెలుగు', 'en' => 'Telugu', 'ar' => 'تيلوغو', 'dir' => 'ltr', 'loc' => 'te_IN', 'font' => 'Noto+Sans+Telugu:wght@400;700;800'],
  'ml' => ['label' => 'മലയാളം', 'en' => 'Malayalam', 'ar' => 'مالايالامي', 'dir' => 'ltr', 'loc' => 'ml_IN', 'font' => 'Noto+Sans+Malayalam:wght@400;700;800'],
  'mr' => ['label' => 'मराठी', 'en' => 'Marathi', 'ar' => 'ماراثي', 'dir' => 'ltr', 'loc' => 'mr_IN', 'font' => 'Noto+Sans+Devanagari:wght@400;700;800'],
  'gu' => ['label' => 'ગુજરાતી', 'en' => 'Gujarati', 'ar' => 'غوجاراتي', 'dir' => 'ltr', 'loc' => 'gu_IN', 'font' => 'Noto+Sans+Gujarati:wght@400;700;800'],
  'pa' => ['label' => 'ਪੰਜਾਬੀ', 'en' => 'Punjabi', 'ar' => 'بنجابي', 'dir' => 'ltr', 'loc' => 'pa_IN', 'font' => 'Noto+Sans+Gurmukhi:wght@400;700;800'],
  'ne' => ['label' => 'नेपाली', 'en' => 'Nepali', 'ar' => 'نيبالي', 'dir' => 'ltr', 'loc' => 'ne_NP', 'font' => 'Noto+Sans+Devanagari:wght@400;700;800'],
  'si' => ['label' => 'සිංහල', 'en' => 'Sinhala', 'ar' => 'سنهالي', 'dir' => 'ltr', 'loc' => 'si_LK', 'font' => 'Noto+Sans+Sinhala:wght@400;700;800'],
  'sw' => ['label' => 'Kiswahili', 'en' => 'Swahili', 'ar' => 'سواحيلي', 'dir' => 'ltr', 'loc' => 'sw_KE', 'font' => 'Noto+Sans:wght@400;700;800'],
];
// Fixed texts of the public page that can be translated / corrected in Admin > Interface texts
const UI_KEYS = ['products', 'all_products', 'location', 'open_maps', 'directions', 'map_title', 'map_explore', 'links_layout', 'lay_list', 'lay_grid', 'close', 'prev', 'next', 'open_now', 'closed_now', 'closed', 'open_24h', 'developed_by', 'day_0', 'day_1', 'day_2', 'day_3', 'day_4', 'day_5', 'day_6', 'search_ph', 'cat_all', 'no_results', 'sale_ends_in', 'cd_d', 'reviews_title', 'write_review', 'your_name', 'your_review', 'rating_label', 'send', 'review_thanks', 'review_err', 'contact_title', 'c_name', 'c_phone', 'c_email', 'c_product', 'c_product_none', 'c_message', 'c_send', 'c_thanks', 'c_err', 'c_need_contact', 'pwa_add', 'pwa_ios', 'rate_err'];

const STR = [
 'en' => [
  'products' => 'Our Products', 'all_products' => 'View all products', 'location' => 'Our location', 'open_maps' => 'Open in Google Maps', 'map_title' => 'Map',
  'menu' => 'Menu', 'stats' => 'Statistics', 'shop_info' => 'Shop info', 'links' => 'Links', 'admin_products' => 'Products', 'view_page' => 'View page', 'logout' => 'Log out', 'admin_panel' => 'Control panel',
  'login' => 'Log in', 'username' => 'Username', 'password' => 'Password', 'sign_in' => 'Sign in', 'bad_login' => 'Wrong username or password.',
  'visits_today' => 'Visits today', 'total_visits' => 'Total visits', 'unique_visitors' => 'Unique visitors', 'total_clicks' => 'Total clicks', 'last14' => 'Last 14 days',
  'top_clicked' => 'Most clicked', 'devices_sources' => 'Devices and sources', 'recent_visits' => 'Recent visits', 'time' => 'Time', 'device' => 'Device', 'browser' => 'Browser', 'source' => 'Source',
  'direct' => 'Direct', 'no_clicks' => 'No clicks yet', 'k_product' => '(product)', 'k_link' => '(link)', 'mobile' => 'Mobile', 'desktop' => 'Desktop', 'tablet' => 'Tablet', 'visitors_title' => 'Visitor statistics',
  'shop_name_en' => 'Shop name (English)', 'shop_name_ar' => 'Shop name (Arabic)', 'tagline_en' => 'Description (English)', 'tagline_ar' => 'Description (Arabic)',
  'cover' => 'Cover photo', 'profile' => 'Profile photo (logo)', 'store_url' => 'Online store link ("View all products" button)', 'map_query' => 'Place name for the map', 'map_link' => 'Google Maps link ("Open in Google Maps" button)',
  'save' => 'Save', 'saved' => 'Information saved.', 'upload_fail' => 'Image upload failed (JPG, PNG or WEBP up to 4MB).',
  'change_pw' => 'Change password', 'current_pw' => 'Current password', 'new_pw' => 'New password (8+ characters)', 'change' => 'Change', 'pw_changed' => 'Password changed.', 'pw_wrong' => 'Current password is wrong.', 'pw_short' => 'New password must be at least 8 characters.',
  'add_link' => 'Add a link', 'edit_link' => 'Edit link', 'type' => 'Type', 'title_en' => 'Button title (English)', 'title_ar' => 'Button title (Arabic)', 'value' => 'Value',
  'value_hint' => 'WhatsApp/Phone: number. Snapchat/TikTok: username or link. Email: address. Other: full link.', 'order' => 'Order', 'visible' => 'Visible on the page', 'save_edit' => 'Save changes', 'add' => 'Add', 'cancel' => 'Cancel',
  'edit' => 'Edit', 'hide' => 'Hide', 'show' => 'Show', 'delete' => 'Delete', 'confirm_del_link' => 'Delete this link permanently?', 'confirm_del_product' => 'Delete this product permanently?',
  'link_added' => 'Link added.', 'link_updated' => 'Link updated.', 'link_deleted' => 'Link deleted.', 'title' => 'Title',
  'add_product' => 'Add a product', 'edit_product' => 'Edit product', 'name_en' => 'Product name (English)', 'name_ar' => 'Product name (Arabic)', 'price' => 'Price', 'product_url' => 'Product link on your website', 'image' => 'Product image', 'product' => 'Product',
  'product_added' => 'Product added.', 'product_updated' => 'Product updated.', 'product_deleted' => 'Product deleted.',
  'type_store' => 'Store', 'type_snapchat' => 'Snapchat', 'type_tiktok' => 'TikTok', 'type_whatsapp' => 'WhatsApp', 'type_phone' => 'Phone', 'type_email' => 'Email', 'type_other' => 'Other link',
  'install_title' => 'Install control panel', 'install_done' => 'Installed. Delete install.php now, then', 'install_login' => 'log in', 'install_exists' => 'Already installed. Delete install.php from the hosting.', 'install_err' => 'Username is required and password must be at least 8 characters.', 'install_btn' => 'Install',
  'anim_repeat' => 'Repeat forever', 'cover_images' => 'Cover images', 'cv_intro' => 'Add more images to the cover and they slide (or fade) one after the other. Upload files or paste image links; you can pick several at once.',
  'cv_main' => 'Show the main cover image first (the one set in Shop info)', 'cv_main_note' => 'Main cover image (change it in Shop info)', 'cv_autoplay' => 'Slide automatically', 'cv_interval' => 'Seconds per image', 'cv_effect' => 'Effect', 'cv_slide' => 'Slide', 'cv_fade' => 'Fade',
  'cv_fit' => 'Image fit', 'cv_fit_contain' => 'Show the whole image', 'cv_fit_cover' => 'Fill the cover (crop the edges)',
  'whatsapp' => 'WhatsApp button', 'wf_enabled' => 'Show the floating WhatsApp button on the page', 'wf_side' => 'Side of the screen', 'side_end' => 'End side (right in English, left in Arabic)', 'side_start' => 'Start side (left in English, right in Arabic)', 'wf_anim' => 'Button animation',
  'wf_none' => 'No animation', 'wf_pulse' => 'Pulse ring', 'wf_ripple' => 'Double ripple', 'wf_bounce' => 'Bounce', 'wf_shake' => 'Shake', 'wf_float' => 'Float up and down',
  'wf_label_en' => 'Text next to the icon (English, optional)', 'wf_label_ar' => 'Text next to the icon (Arabic, optional)', 'wf_msg_en' => 'Ready message (English)', 'wf_msg_ar' => 'Ready message (Arabic)',
  'wf_number_note' => 'The number is taken from "WhatsApp number for orders" in Appearance, or from your first WhatsApp link. Current number:', 'k_wafloat' => '(WhatsApp button)',
  'ip_col' => 'IP address', 'store_ip' => 'Store and show the IP address of every visitor', 'ip_note' => 'IP addresses are personal data in many countries, so mention it in a privacy notice, or turn this off. Visits recorded before this option only have a scrambled IP and show "-".', 'ip_saved' => 'Saved.',
  'pos_after_links' => 'Below the link buttons', 'pos_after_products' => 'Below the products', 'pos_before_hours' => 'Above the opening hours', 'pos_after_hours' => 'Below the opening hours', 'pos_before_reviews' => 'Above the customer reviews', 'pos_after_reviews' => 'Below the customer reviews', 'pos_before_contact' => 'Above the contact form', 'pos_after_contact' => 'Below the contact form', 'pos_after_map' => 'Below the map',
  'page_layout' => 'Page layout', 'pl_intro' => 'Move the sections of the page up or down. Headings and banners can be placed above or below ANY section (see the Headings and Banners menus). Hide a section from its own menu.', 'pl_up' => 'Move up', 'pl_down' => 'Move down', 'pl_saved' => 'Page layout saved.', 'pl_note' => 'Where this section appears on the page is set in "Page layout".',
  'sn_links' => 'Link buttons', 'sn_products' => 'Products', 'sn_hours' => 'Opening hours', 'sn_reviews' => 'Customer reviews', 'sn_contact' => 'Contact / order form', 'sn_map' => 'Map',
  'use_text_color' => 'Use a text color', 'use_bg_color' => 'Use a background color (leave unchecked for no background)',
  'sec_anim' => 'Page animations (shop name, slogan, logo, cover)', 'anim_enabled' => 'Turn the page animations on', 'anim_cover' => 'Cover photo animation', 'anim_logo' => 'Profile photo (logo) animation', 'anim_name' => 'Shop name animation', 'anim_tagline' => 'Slogan (description) animation',
  'an_none' => 'No animation', 'an_fade' => 'Fade in', 'an_slide_up' => 'Slide up', 'an_slide_down' => 'Slide down', 'an_slide_left' => 'Slide from the left', 'an_slide_right' => 'Slide from the right', 'an_zoom' => 'Zoom in', 'an_bounce' => 'Bounce', 'an_flip' => 'Flip', 'an_typewriter' => 'Typewriter (text only)', 'an_glow' => 'Glow (repeats)', 'an_pulse' => 'Pulse (repeats)', 'an_shimmer' => 'Shimmer (text, repeats)', 'an_float' => 'Float (repeats)',
  'cd_mode' => 'Sale countdown', 'cdm_default' => 'Follow the default', 'cdm_show' => 'Always show (when the sale has an end date)', 'cdm_hide' => 'Hide', 'countdown_default' => 'Show a countdown on every sale that has an end date (each product can change this)',
  'os_android' => 'Android phone', 'os_iphone' => 'iPhone', 'os_ipad' => 'iPad', 'os_android_tablet' => 'Android tablet', 'os_windows' => 'Windows PC', 'os_mac' => 'Mac', 'os_linux' => 'Linux PC', 'os_chromeos' => 'Chromebook', 'os_other' => 'Other device',
  'del_visit' => 'Delete', 'confirm_del_visit' => 'Delete this visit?', 'del_all_visits' => 'Delete all visits', 'confirm_del_all' => 'Delete ALL visits? This cannot be undone.', 'visits_deleted' => 'Visits deleted.',
  'bad_dates' => 'The sale end must be after the sale start (the dates were not saved).', 'sec_orders' => 'Orders (WhatsApp / order form)', 'sec_catalog' => 'Search and categories', 'mark_all_read' => 'Mark all as read', 'msg_new' => 'New', 'rv_by' => 'by',
  'categories' => 'Categories', 'add_category' => 'Add a category', 'edit_category' => 'Edit category', 'cat_name_en' => 'Category name (English)', 'cat_name_ar' => 'Category name (Arabic)', 'cat_added' => 'Category added.', 'cat_updated' => 'Category updated.', 'cat_deleted' => 'Category deleted.', 'confirm_del_cat' => 'Delete this category? Its products stay but lose the category.', 'category' => 'Category', 'no_category' => 'No category',
  'catalog_search' => 'Show the search box and category buttons above the products',
  'buy_mode' => 'What the product button does', 'bm_default' => 'Use the default', 'bm_link' => 'Open the product link', 'bm_wa' => 'Order by WhatsApp', 'bm_form' => 'Open the order form',
  'sale_start' => 'Sale starts (optional)', 'sale_end' => 'Sale ends (optional)', 'show_countdown' => 'Show a countdown until the sale ends', 'sale_off' => 'sale off',
  'buy_mode_default' => 'Default action of the product button', 'wa_number' => 'WhatsApp number for orders (country code + number, digits only; empty = the first WhatsApp link)', 'wa_msg_en' => 'Order message (English)', 'wa_msg_ar' => 'Order message (Arabic)', 'wa_msg_hint' => 'Use {name}, {price} and {shop} inside the message.',
  'sec_pwa' => 'Add to home screen (install like an app)', 'pwa_enabled' => 'Let visitors install the page like an app', 'pwa_install' => 'Show an "Add to home screen" button on the page', 'pwa_color' => 'App color (address bar and splash screen)', 'pwa_short_en' => 'Short app name under the icon (English, optional)', 'pwa_short_ar' => 'Short app name under the icon (Arabic, optional)',
  'reviews' => 'Reviews', 'reviews_enabled' => 'Show the customer reviews section', 'reviews_submit' => 'Let visitors write reviews (they appear after you approve them)', 'reviews_limit' => 'How many reviews to show', 'rv_add' => 'Add a review', 'rv_edit' => 'Edit review', 'rv_name' => 'Customer name', 'rv_rating' => 'Rating (1 to 5)', 'rv_text' => 'Review text', 'rv_pending' => 'Waiting for approval', 'rv_approved' => 'Approved', 'rv_approve' => 'Approve', 'rv_hide' => 'Move back to waiting', 'rv_added' => 'Review added.', 'rv_updated' => 'Review updated.', 'rv_deleted' => 'Review deleted.', 'rv_none' => 'Nothing here yet.', 'confirm_del_rv' => 'Delete this review?',
  'messages' => 'Messages', 'contact_enabled' => 'Show the contact / order form on the page', 'contact_title_en' => 'Form title (English)', 'contact_title_ar' => 'Form title (Arabic)', 'notify_enabled' => 'Send me an email for every new message', 'notify_email' => 'Email address for alerts',
  'notify_note' => 'Email alerts use the web server mail function. Some free hosts (including InfinityFree) block it, so also check this inbox: the sidebar shows a red number for unread messages.', 'msg_inbox' => 'Inbox', 'msg_from' => 'From', 'msg_product' => 'Product', 'mark_read' => 'Mark as read', 'mark_unread' => 'Mark as unread', 'msg_deleted' => 'Message deleted.', 'confirm_del_msg' => 'Delete this message?', 'no_messages' => 'No messages yet.', 'reply_wa' => 'WhatsApp', 'reply_call' => 'Call', 'reply_mail' => 'Email',
  'seo' => 'SEO', 'seo_title_en' => 'Page title (English)', 'seo_title_ar' => 'Page title (Arabic)', 'seo_desc_en' => 'Page description (English)', 'seo_desc_ar' => 'Page description (Arabic)', 'seo_index' => 'Let search engines (Google) list this page',
  'seo_intro' => 'The title and description are what Google shows in search results (title about 60 characters, description about 155). Business data (name, location, phone, opening hours) and language links are added automatically.', 'k_order' => '(WhatsApp order)',
  'search_ph' => 'Search products…', 'cat_all' => 'All', 'no_results' => 'No products found', 'sale_ends_in' => 'Sale ends in', 'cd_d' => 'd', 'reviews_title' => 'Customer reviews', 'write_review' => 'Write a review', 'your_name' => 'Your name', 'your_review' => 'Your review', 'rating_label' => 'Rating', 'send' => 'Send', 'review_thanks' => 'Thank you! Your review will appear after approval.', 'review_err' => 'Could not send, please try again.', 'contact_title' => 'Contact us', 'c_name' => 'Your name', 'c_phone' => 'Phone / WhatsApp', 'c_email' => 'Email (optional)', 'c_product' => 'Product (optional)', 'c_product_none' => '— Choose a product —', 'c_message' => 'Message', 'c_send' => 'Send', 'c_thanks' => 'Thank you! We received your message and will contact you soon.', 'c_err' => 'Could not send, please try again.', 'c_need_contact' => 'Please enter a phone number or an email.', 'pwa_add' => 'Add to home screen', 'pwa_ios' => 'Tap the Share button, then choose "Add to Home Screen".', 'rate_err' => 'Too many attempts, please try later.',
  'lang1_label' => 'Language 1 (first language of the pair)', 'lang2_label' => 'Language 2 (second language of the pair)', 'default_lang_label' => 'Language shown first to new visitors', 'dl_first' => 'Language 1', 'dl_second' => 'Language 2',
  'lang_note' => 'Choose ANY two languages (default: English / Arabic). The pair is used by the public page and by this admin panel: the language buttons and the labels of the text boxes follow it. Texts you type in the Language 1 / Language 2 boxes are shown in the matching language, so after changing the pair please rewrite those boxes. The admin menus are fully translated into English and Arabic only; for other languages the menu text stays English.',
  'if_intro' => 'These are the fixed texts of the public page (titles, map buttons, days, badges) for your two languages: %s. Built-in translations exist for some languages; fill in or correct any box. An empty box uses the built-in text, or English.', 'english_text' => 'English text',
  'tl_add' => 'Add a tracking link', 'tl_edit' => 'Edit tracking link', 'tl_name' => 'Link name (for you only)', 'tl_visits' => 'Visits', 'tl_saved' => 'Tracking link saved.', 'tl_deleted' => 'Tracking link deleted.', 'tl_confirm_del' => 'Delete this tracking link? Past statistics are kept.', 'tl_need_source' => 'The source name is required.',
  'tl_quick' => 'Quick start (tap to fill the form)', 'tl_none' => 'No tracking links yet.', 'k_map' => '(map)', 'map_open_label' => 'Map: Open in Google Maps', 'map_dir_label' => 'Map: Directions',
  'n_stats' => 'Statistics',
  'interface_texts' => 'Interface texts', 'if_saved' => 'Interface texts saved.', 'sz_xl' => 'Extra large', 'sz_xxl' => 'Huge',
  'site_langs' => 'Site languages', 'lang_ar' => 'Arabic (عربي)', 'lang_bn' => 'Bengali (বাংলা)', 'lang_hi' => 'Hindi (हिन्दी)',
  'map_kicker_en' => 'Small title above the name in the map box (English, empty = "Our location")', 'map_kicker_ar' => 'Small title above the name in the map box (Arabic, empty = "موقعنا")',
  'regular_price' => 'Regular price', 'sale_price' => 'Sale price (optional)', 'currency' => 'Currency', 'cur_default' => 'Default currency (used by products that do not choose one)', 'use_default' => 'Use the default currency',
  'show_discount' => 'Show a discount badge (for example -20%) on products that have a sale price', 'price_text' => 'Custom price text (optional, used only when there is no regular price, for example "Free" or "Ask for price")',
  'cur_saved' => 'Currency settings saved.', 'sale_lower' => 'The sale price must be lower than the regular price (the sale price was not saved).', 'sale_needs_regular' => 'Add a regular price before setting a sale price.', 'bad_price' => 'Prices must be numbers.', 'sec_prices' => 'Prices and currency', 'was' => 'was',
  'hours' => 'Opening hours', 'hours_enabled' => 'Show the opening hours section', 'hours_badge' => 'Show an "Open now / Closed now" badge', 'hours_title_en' => 'Section title (English)', 'hours_title_ar' => 'Section title (Arabic)',
  'hours_pos' => 'Position (where it appears)', 'hours_fmt' => 'Time format', 'fmt_12' => '12-hour (AM / PM)', 'fmt_24' => '24-hour', 'hours_start' => 'First day shown',
  'closed' => 'Closed', 'all_day' => 'Open 24 hours', 'open_24h' => 'Open 24 hours', 'shift1' => 'Shift 1', 'shift2' => 'Shift 2 (optional)', 'opens' => 'Opens', 'closes' => 'Closes', 'open_now' => 'Open now', 'closed_now' => 'Closed now', 'day' => 'Day',
  'hours_saved' => 'Opening hours saved.', 'hours_note' => 'Leave a time empty to skip it. Use Shift 2 when the shop closes for a break and opens again. A closing time earlier than the opening time means closing after midnight.',
  'day_0' => 'Sunday', 'day_1' => 'Monday', 'day_2' => 'Tuesday', 'day_3' => 'Wednesday', 'day_4' => 'Thursday', 'day_5' => 'Friday', 'day_6' => 'Saturday',
  'price_hint' => 'Write the amount then SAR (example: 25 SAR). It is shown with the new Saudi riyal symbol.',
  'map_embed' => 'Google Maps embed code (best option)', 'map_embed_hint' => 'In Google Maps open your place > Share > Embed a map > Copy HTML, then paste it here. Visitors then see your real Google place, and tapping the pin shows its details. If empty, the coordinates above are used.', 'bad_embed' => 'This is not a Google Maps embed code.',
  'stat_visits' => 'Visits', 'stat_unique' => 'Unique visitors', 'stat_clicks' => 'Clicks', 'visits_over_time' => 'Visits over time',
  'src_email' => 'Email', 'campaigns' => 'Campaigns (UTM)', 'no_campaign' => 'No tracked campaigns yet', 'per_page' => 'Rows per page', 'page_x_of' => 'Page %d of %d', 'medium' => 'Medium', 'campaign_name' => 'Campaign name', 'utm_source_name' => 'Source name (example: whatsapp)',
  'utm_intro' => 'Every link below carries UTM tags (utm_source, utm_medium, utm_campaign). Share THESE links (not the plain website link) in WhatsApp, Facebook, Instagram, emails and QR codes, and the statistics will show exactly where each visitor came from.',
  'utm_why' => 'Why: chat apps like WhatsApp hide where a click came from, so a plain shared link is counted as "Direct". A link with UTM tags is always recognized.', 'qr_fail' => 'Could not load the QR library. Check the internet connection and reload the page.',
  'med_social' => 'Social media', 'med_qr' => 'QR code', 'med_message' => 'Message / chat', 'med_email' => 'Email', 'med_referral' => 'Referral', 'med_banner' => 'Banner / ad', 'med_other' => 'Other',
  'images_multi' => 'Upload images (you can select several at once)', 'image_urls' => 'Or paste image links (one link per line)', 'slides_added' => 'Added %d slide(s).',
  'sec_buy' => 'Product button (Buy now / Order now)', 'buy_enabled' => 'Show a button on each product', 'buy_text_en' => 'Default button text (English)', 'buy_text_ar' => 'Default button text (Arabic)', 'buy_bg' => 'Product button background color', 'buy_fg' => 'Product button text color',
  'pbtn_en' => 'Button text for this product (English, optional)', 'pbtn_ar' => 'Button text for this product (Arabic, optional)', 'pbtn_hint' => 'Leave empty to use the default text from Appearance (for example "Buy now" or "Order now"). The button opens the product link above.',
  'banners' => 'Banners', 'bn_intro' => 'Create a slider, choose where it appears on the page, then add slides (images with optional links). If a heading is in the same position, the heading is shown first and the slider under it.',
  'add_slider' => 'Add a banner slider', 'edit_slider' => 'Edit slider', 'slider_name' => 'Slider name (for you only)', 'slider_position' => 'Position (where it appears)', 'bpos_top' => 'Top of the page (above the cover)',
  'autoplay' => 'Auto-play', 'interval' => 'Seconds per slide', 'ratio' => 'Banner shape', 'r_3_1' => 'Wide strip (3:1)', 'r_2_1' => 'Wide (2:1)', 'r_16_9' => 'Video shape (16:9)', 'r_4_3' => 'Classic (4:3)', 'r_1_1' => 'Square (1:1)',
  'fit' => 'Image fit', 'fit_cover' => 'Fill the banner (crop edges)', 'fit_contain' => 'Show the whole image', 'show_arrows' => 'Show arrows', 'show_dots' => 'Show dots',
  'manage_slides' => 'Manage slides', 'slides_count' => 'Slides', 'slider_added' => 'Slider added.', 'slider_updated' => 'Slider updated.', 'slider_deleted' => 'Slider deleted.', 'confirm_del_slider' => 'Delete this slider and all its slides?',
  'slides' => 'Slides', 'add_slide' => 'Add a slide', 'edit_slide' => 'Edit slide', 'slide_image' => 'Slide image', 'caption_en' => 'Caption (English, optional)', 'caption_ar' => 'Caption (Arabic, optional)', 'slide_link' => 'Link when tapped (optional)',
  'slide_added' => 'Slide added.', 'slide_updated' => 'Slide updated.', 'slide_deleted' => 'Slide deleted.', 'confirm_del_slide' => 'Delete this slide?', 'need_image' => 'Please upload an image or paste an image link.', 'back_banners' => 'Back to banners',
  'no_slides' => 'No slides yet.', 'k_banner' => '(banner)', 'prev' => 'Previous', 'next' => 'Next', 'slider' => 'Slider',
  'headings' => 'Headings', 'add_heading' => 'Add a heading', 'edit_heading' => 'Edit heading', 'position' => 'Position (where it appears)',
  'pos_top_bar' => 'Top bar (colored strip above the cover)', 'pos_after_profile' => 'Under the shop name and description', 'pos_before_links' => 'Above the link buttons', 'pos_before_products' => 'Above the products', 'pos_before_map' => 'Above the map', 'pos_bottom' => 'Bottom (above the footer)',
  'hd_text_en' => 'Heading text (English)', 'hd_text_ar' => 'Heading text (Arabic)', 'hd_sub_en' => 'Small text under it (English, optional)', 'hd_sub_ar' => 'Small text under it (Arabic, optional)', 'hd_link' => 'Link (optional)',
  'align' => 'Alignment', 'al_start' => 'Start', 'al_center' => 'Center', 'al_end' => 'End', 'size' => 'Size', 'sz_sm' => 'Small', 'sz_md' => 'Medium', 'sz_lg' => 'Large',
  'heading_added' => 'Heading added.', 'heading_updated' => 'Heading updated.', 'heading_deleted' => 'Heading deleted.', 'confirm_del_heading' => 'Delete this heading?',
  'hd_intro' => 'Add as many headings as you need and choose where each one appears. Several headings in the same place are shown by their order number. Top bar headings use the colors set in Appearance unless you give them their own.',
  'sec_topbar' => 'Top bar style', 'admin_preloader' => 'Also show the loading screen in the admin panel (all backend pages)', 'text' => 'Text',
  'sec_footer' => 'Footer (shown on all pages)', 'footer_enabled' => 'Show the footer', 'footer_text_en' => 'Footer text (English)', 'footer_text_ar' => 'Footer text (Arabic)', 'footer_link_en' => 'Footer link text (English)', 'footer_link_ar' => 'Footer link text (Arabic)', 'footer_url' => 'Footer link URL',
  'sec_headings' => 'Section headings', 'hd_links' => 'Heading above the link buttons', 'hd_products' => 'Heading above the products', 'hd_map' => 'Heading above the map', 'hd_show' => 'Show this heading', 'hd_en' => 'Heading text (English)', 'hd_ar' => 'Heading text (Arabic)', 'hd_hint' => 'Example: "All products". Leave the box unchecked to hide the heading.',
  'map_explore' => 'Explore map',
  'appearance' => 'Appearance', 'sec_buttons' => 'Link buttons colors', 'btn_mode' => 'Buttons color mode', 'btn_mode_brand' => 'Brand colors (each button type has its own color)', 'btn_mode_custom' => 'One custom color for all buttons',
  'btn_bg' => 'Button background color', 'btn_text' => 'Button text color', 'custom_colors' => 'Custom colors for this button', 'bg_color' => 'Background color', 'text_color' => 'Text color',
  'sec_headline' => 'Headline bar (top of the page)', 'headline_enabled' => 'Show the headline bar', 'headline_en' => 'Headline text (English)', 'headline_ar' => 'Headline text (Arabic)', 'headline_url' => 'Headline link (optional)', 'headline_bg' => 'Headline background color', 'headline_fg' => 'Headline text color', 'headline_scroll' => 'Scrolling text',
  'sec_preloader' => 'Preloader', 'preloader_enabled' => 'Show the loading screen (logo animation) while the page loads',
  'sec_popup' => 'Welcome popup', 'popup_enabled' => 'Show the welcome popup', 'popup_title_en' => 'Popup title (English)', 'popup_title_ar' => 'Popup title (Arabic)', 'popup_text_en' => 'Popup message (English)', 'popup_text_ar' => 'Popup message (Arabic)',
  'popup_btn_en' => 'Popup button text (English)', 'popup_btn_ar' => 'Popup button text (Arabic)', 'popup_btn_url' => 'Popup button link (optional, empty = just closes)', 'popup_freq' => 'How often to show it', 'pf_always' => 'Every visit (every page load)', 'pf_session' => 'Once per browser session', 'pf_day' => 'Once a day', 'pf_once' => 'Only once ever',
  'popup_delay' => 'Show after (seconds)', 'close' => 'Close', 'appearance_saved' => 'Appearance saved.',
  'tracking' => 'QR & tracking links', 'live_visitors' => 'Live visitors', 'unknown' => 'Unknown', 'range_7' => 'Last 7 days', 'range_30' => 'Last 30 days', 'range_all' => 'All time', 'sources' => 'Traffic sources',
  'src_qr' => 'QR code', 'src_search' => 'Search engine', 'src_social' => 'Social media', 'src_referral' => 'Other website', 'src_campaign' => 'Tracked link', 'src_direct' => 'Direct / unknown',
  'src_detail' => 'Source details', 'countries' => 'Countries', 'cities' => 'Cities / regions', 'visitor_location' => 'Location', 'no_data' => 'No data yet', 'visits_col' => 'Visits',
  'geo_note' => 'Location is approximate (from the IP address): country and city/region. It cannot show the exact district, and mobile networks often show the nearest big city.',
  'src_note' => 'Visitors who open the link from apps that hide the origin (such as WhatsApp or Snapchat chats) appear as Direct. Use tracking links to measure them.',
  'qr_intro' => 'Use these links in your QR codes and posts. The statistics will then show exactly where visitors come from.', 'qr_custom' => 'Custom source name (letters, numbers, - or _)', 'qr_make' => 'Create',
  'qr_download' => 'Download QR', 'qr_copy' => 'Copy link', 'qr_copied' => 'Copied', 'qr_main' => 'QR code (flyers, menu, shop door)',
  'favicon' => 'Favicon (small icon in the browser tab)', 'og_image' => 'Link preview image (shown when the link is shared)', 'og_hint' => 'Recommended 1200x630. If empty, the cover photo is used.', 'fav_hint' => 'Square PNG, at least 192x192. If empty, the profile photo is used.',
  'directions' => 'Directions', 'developed_by' => 'Developed by', 'links_layout' => 'Buttons layout', 'layout_list' => 'List (one per row)', 'layout_grid' => 'Grid (2 per row)', 'lay_list' => 'List view', 'lay_grid' => 'Grid view',
  'icon_removed' => 'Custom icon removed.', 'image_removed' => 'Image removed.',
  'admins' => 'Admins', 'add_admin' => 'Add an admin', 'edit_admin' => 'Edit admin', 'full_name' => 'Name', 'email' => 'Email', 'phone' => 'Phone', 'login_id' => 'Username, email or phone',
  'pin_hint' => 'Password: digits only, 4 to 6 digits.', 'pin_invalid' => 'Password must be digits only, 4 to 6 digits.', 'new_pw' => 'New password (4-6 digits)', 'pw_short' => 'Password must be digits only, 4 to 6 digits.',
  'admin_added' => 'Admin added.', 'admin_updated' => 'Admin updated.', 'admin_deleted' => 'Admin deleted.', 'confirm_del_admin' => 'Delete this admin?', 'cant_delete_self' => 'You cannot delete your own account.', 'cant_delete_last' => 'You cannot delete the last admin.',
  'dup_admin' => 'This username, email or phone is already used.', 'need_identifier' => 'Enter at least a username, email or phone.', 'locked' => 'Too many attempts. Try again in 10 minutes.', 'leave_blank' => 'Leave blank to keep the current password', 'you' => '(you)',
  'online_now' => 'Online now', 'map_coords' => 'Location coordinates (latitude, longitude)', 'map_coords_hint' => 'In Google Maps: press and hold on your place, then copy the numbers shown (example: 25.8694, 43.4973).', 'bad_coords' => 'Coordinates are not valid.',
  'map_name_en' => 'Location name on the map (English)', 'map_name_ar' => 'Location name on the map (Arabic)', 'image_url' => 'Or paste an image link', 'remove_image' => 'Remove image', 'bad_url' => 'Image link must start with http:// or https://',
  'custom_icon' => 'Custom icon (optional, PNG/JPG/WEBP)', 'remove_icon' => 'Remove custom icon', 'password_label' => 'Password', 'name_col' => 'Name',
 ],
 'ar' => [
  'products' => 'منتجاتنا', 'all_products' => 'عرض كل المنتجات', 'location' => 'موقعنا', 'open_maps' => 'فتح في خرائط Google', 'map_title' => 'الخريطة',
  'menu' => 'القائمة', 'stats' => 'الإحصائيات', 'shop_info' => 'معلومات المتجر', 'links' => 'الروابط', 'admin_products' => 'المنتجات', 'view_page' => 'عرض الصفحة', 'logout' => 'تسجيل الخروج', 'admin_panel' => 'لوحة التحكم',
  'login' => 'تسجيل الدخول', 'username' => 'اسم المستخدم', 'password' => 'كلمة المرور', 'sign_in' => 'دخول', 'bad_login' => 'اسم المستخدم أو كلمة المرور غير صحيحة.',
  'visits_today' => 'زيارات اليوم', 'total_visits' => 'إجمالي الزيارات', 'unique_visitors' => 'زوار مختلفون', 'total_clicks' => 'إجمالي النقرات', 'last14' => 'آخر 14 يوماً',
  'top_clicked' => 'الأكثر نقراً', 'devices_sources' => 'الأجهزة والمصادر', 'recent_visits' => 'آخر الزيارات', 'time' => 'الوقت', 'device' => 'الجهاز', 'browser' => 'المتصفح', 'source' => 'المصدر',
  'direct' => 'مباشر', 'no_clicks' => 'لا توجد نقرات بعد', 'k_product' => '(منتج)', 'k_link' => '(رابط)', 'mobile' => 'جوال', 'desktop' => 'كمبيوتر', 'tablet' => 'تابلت', 'visitors_title' => 'إحصائيات الزوار',
  'shop_name_en' => 'اسم المتجر (إنجليزي)', 'shop_name_ar' => 'اسم المتجر (عربي)', 'tagline_en' => 'الوصف (إنجليزي)', 'tagline_ar' => 'الوصف (عربي)',
  'cover' => 'صورة الغلاف', 'profile' => 'الصورة الشخصية (الشعار)', 'store_url' => 'رابط المتجر الإلكتروني (زر «عرض كل المنتجات»)', 'map_query' => 'اسم المكان للخريطة', 'map_link' => 'رابط خرائط Google (زر «فتح في خرائط Google»)',
  'save' => 'حفظ', 'saved' => 'تم حفظ المعلومات.', 'upload_fail' => 'تعذر رفع الصورة (JPG أو PNG أو WEBP حتى 4MB).',
  'change_pw' => 'تغيير كلمة المرور', 'current_pw' => 'كلمة المرور الحالية', 'new_pw' => 'كلمة المرور الجديدة (8 أحرف فأكثر)', 'change' => 'تغيير', 'pw_changed' => 'تم تغيير كلمة المرور.', 'pw_wrong' => 'كلمة المرور الحالية غير صحيحة.', 'pw_short' => 'كلمة المرور الجديدة 8 أحرف على الأقل.',
  'add_link' => 'إضافة رابط', 'edit_link' => 'تعديل رابط', 'type' => 'النوع', 'title_en' => 'عنوان الزر (إنجليزي)', 'title_ar' => 'عنوان الزر (عربي)', 'value' => 'القيمة',
  'value_hint' => 'واتساب/اتصال: الرقم. سناب/تيك توك: اسم المستخدم أو الرابط. بريد: العنوان. غير ذلك: الرابط كاملاً.', 'order' => 'الترتيب', 'visible' => 'ظاهر في الصفحة', 'save_edit' => 'حفظ التعديل', 'add' => 'إضافة', 'cancel' => 'إلغاء',
  'edit' => 'تعديل', 'hide' => 'إخفاء', 'show' => 'إظهار', 'delete' => 'حذف', 'confirm_del_link' => 'حذف هذا الرابط نهائياً؟', 'confirm_del_product' => 'حذف هذا المنتج نهائياً؟',
  'link_added' => 'تمت إضافة الرابط.', 'link_updated' => 'تم تحديث الرابط.', 'link_deleted' => 'تم حذف الرابط.', 'title' => 'العنوان',
  'add_product' => 'إضافة منتج', 'edit_product' => 'تعديل منتج', 'name_en' => 'اسم المنتج (إنجليزي)', 'name_ar' => 'اسم المنتج (عربي)', 'price' => 'السعر', 'product_url' => 'رابط المنتج في موقعك', 'image' => 'صورة المنتج', 'product' => 'المنتج',
  'product_added' => 'تمت إضافة المنتج.', 'product_updated' => 'تم تحديث المنتج.', 'product_deleted' => 'تم حذف المنتج.',
  'type_store' => 'المتجر', 'type_snapchat' => 'سناب شات', 'type_tiktok' => 'تيك توك', 'type_whatsapp' => 'واتساب', 'type_phone' => 'اتصال', 'type_email' => 'بريد إلكتروني', 'type_other' => 'رابط آخر',
  'install_title' => 'تثبيت لوحة التحكم', 'install_done' => 'تم التثبيت. احذف ملف install.php الآن ثم', 'install_login' => 'سجّل الدخول', 'install_exists' => 'التثبيت مكتمل مسبقاً. احذف ملف install.php من الاستضافة.', 'install_err' => 'اسم المستخدم مطلوب وكلمة المرور 8 أحرف على الأقل.', 'install_btn' => 'تثبيت',
  'anim_repeat' => 'تكرار دائم', 'cover_images' => 'صور الغلاف', 'cv_intro' => 'أضف صوراً أخرى للغلاف لتتحرك (أو تتلاشى) واحدة بعد الأخرى. ارفع ملفات أو الصق روابط صور، ويمكنك اختيار عدة صور معاً.',
  'cv_main' => 'إظهار صورة الغلاف الرئيسية أولاً (المحددة في معلومات المتجر)', 'cv_main_note' => 'صورة الغلاف الرئيسية (غيّرها من معلومات المتجر)', 'cv_autoplay' => 'تحريك تلقائي', 'cv_interval' => 'ثواني لكل صورة', 'cv_effect' => 'التأثير', 'cv_slide' => 'انزلاق', 'cv_fade' => 'تلاشي',
  'cv_fit' => 'ملاءمة الصورة', 'cv_fit_contain' => 'إظهار الصورة كاملة', 'cv_fit_cover' => 'ملء الغلاف (قص الأطراف)',
  'whatsapp' => 'زر واتساب', 'wf_enabled' => 'إظهار زر واتساب العائم في الصفحة', 'wf_side' => 'جهة الشاشة', 'side_end' => 'جهة النهاية (يمين في الإنجليزية، يسار في العربية)', 'side_start' => 'جهة البداية (يسار في الإنجليزية، يمين في العربية)', 'wf_anim' => 'حركة الزر',
  'wf_none' => 'بدون حركة', 'wf_pulse' => 'حلقة نابضة', 'wf_ripple' => 'موجتان', 'wf_bounce' => 'ارتداد', 'wf_shake' => 'اهتزاز', 'wf_float' => 'طفو للأعلى والأسفل',
  'wf_label_en' => 'نص بجانب الأيقونة (إنجليزي، اختياري)', 'wf_label_ar' => 'نص بجانب الأيقونة (عربي، اختياري)', 'wf_msg_en' => 'الرسالة الجاهزة (إنجليزي)', 'wf_msg_ar' => 'الرسالة الجاهزة (عربي)',
  'wf_number_note' => 'الرقم يؤخذ من «رقم واتساب للطلبات» في المظهر، أو من أول رابط واتساب عندك. الرقم الحالي:', 'k_wafloat' => '(زر واتساب)',
  'ip_col' => 'عنوان IP', 'store_ip' => 'حفظ وعرض عنوان IP لكل زائر', 'ip_note' => 'عناوين IP بيانات شخصية في كثير من الدول، لذلك اذكر ذلك في إشعار الخصوصية، أو أوقف هذا الخيار. الزيارات المسجلة قبل هذا الخيار لها IP مشفّر فقط وتظهر «-».', 'ip_saved' => 'تم الحفظ.',
  'pos_after_links' => 'تحت أزرار الروابط', 'pos_after_products' => 'تحت المنتجات', 'pos_before_hours' => 'فوق مواعيد العمل', 'pos_after_hours' => 'تحت مواعيد العمل', 'pos_before_reviews' => 'فوق آراء العملاء', 'pos_after_reviews' => 'تحت آراء العملاء', 'pos_before_contact' => 'فوق نموذج التواصل', 'pos_after_contact' => 'تحت نموذج التواصل', 'pos_after_map' => 'تحت الخريطة',
  'page_layout' => 'ترتيب الصفحة', 'pl_intro' => 'حرّك أقسام الصفحة للأعلى أو للأسفل. يمكن وضع العناوين والبانرات فوق أو تحت أي قسم (من قائمتي العناوين والبانرات). لإخفاء قسم استخدم قائمته الخاصة.', 'pl_up' => 'تحريك للأعلى', 'pl_down' => 'تحريك للأسفل', 'pl_saved' => 'تم حفظ ترتيب الصفحة.', 'pl_note' => 'مكان ظهور هذا القسم في الصفحة يُحدد من «ترتيب الصفحة».',
  'sn_links' => 'أزرار الروابط', 'sn_products' => 'المنتجات', 'sn_hours' => 'مواعيد العمل', 'sn_reviews' => 'آراء العملاء', 'sn_contact' => 'نموذج التواصل / الطلب', 'sn_map' => 'الخريطة',
  'use_text_color' => 'استخدام لون للنص', 'use_bg_color' => 'استخدام لون للخلفية (بدون علامة = بلا خلفية)',
  'sec_anim' => 'حركات الصفحة (اسم المتجر، الشعار، الصورة، الغلاف)', 'anim_enabled' => 'تشغيل حركات الصفحة', 'anim_cover' => 'حركة صورة الغلاف', 'anim_logo' => 'حركة الصورة الشخصية (الشعار)', 'anim_name' => 'حركة اسم المتجر', 'anim_tagline' => 'حركة الشعار (الوصف)',
  'an_none' => 'بدون حركة', 'an_fade' => 'ظهور تدريجي', 'an_slide_up' => 'انزلاق للأعلى', 'an_slide_down' => 'انزلاق للأسفل', 'an_slide_left' => 'انزلاق من اليسار', 'an_slide_right' => 'انزلاق من اليمين', 'an_zoom' => 'تكبير', 'an_bounce' => 'ارتداد', 'an_flip' => 'قلب', 'an_typewriter' => 'آلة كاتبة (للنص فقط)', 'an_glow' => 'توهج (يتكرر)', 'an_pulse' => 'نبض (يتكرر)', 'an_shimmer' => 'لمعان (للنص، يتكرر)', 'an_float' => 'طفو (يتكرر)',
  'cd_mode' => 'العدّاد التنازلي للتخفيض', 'cdm_default' => 'اتباع الافتراضي', 'cdm_show' => 'إظهار دائماً (عند وجود تاريخ نهاية)', 'cdm_hide' => 'إخفاء', 'countdown_default' => 'إظهار عدّاد تنازلي لكل تخفيض له تاريخ نهاية (ويمكن تغييره لكل منتج)',
  'os_android' => 'هاتف أندرويد', 'os_iphone' => 'آيفون', 'os_ipad' => 'آيباد', 'os_android_tablet' => 'تابلت أندرويد', 'os_windows' => 'كمبيوتر ويندوز', 'os_mac' => 'ماك', 'os_linux' => 'كمبيوتر لينكس', 'os_chromeos' => 'كروم بوك', 'os_other' => 'جهاز آخر',
  'del_visit' => 'حذف', 'confirm_del_visit' => 'حذف هذه الزيارة؟', 'del_all_visits' => 'حذف كل الزيارات', 'confirm_del_all' => 'حذف كل الزيارات؟ لا يمكن التراجع.', 'visits_deleted' => 'تم حذف الزيارات.',
  'bad_dates' => 'نهاية التخفيض يجب أن تكون بعد بدايته (لم تُحفظ التواريخ).', 'sec_orders' => 'الطلبات (واتساب / نموذج الطلب)', 'sec_catalog' => 'البحث والأقسام', 'mark_all_read' => 'تعليم الكل كمقروء', 'msg_new' => 'جديد', 'rv_by' => 'بواسطة',
  'categories' => 'الأقسام', 'add_category' => 'إضافة قسم', 'edit_category' => 'تعديل قسم', 'cat_name_en' => 'اسم القسم (إنجليزي)', 'cat_name_ar' => 'اسم القسم (عربي)', 'cat_added' => 'تمت إضافة القسم.', 'cat_updated' => 'تم تحديث القسم.', 'cat_deleted' => 'تم حذف القسم.', 'confirm_del_cat' => 'حذف هذا القسم؟ منتجاته تبقى لكنها تفقد القسم.', 'category' => 'القسم', 'no_category' => 'بدون قسم',
  'catalog_search' => 'إظهار مربع البحث وأزرار الأقسام فوق المنتجات',
  'buy_mode' => 'ماذا يفعل زر المنتج', 'bm_default' => 'استخدام الافتراضي', 'bm_link' => 'فتح رابط المنتج', 'bm_wa' => 'الطلب عبر واتساب', 'bm_form' => 'فتح نموذج الطلب',
  'sale_start' => 'بداية التخفيض (اختياري)', 'sale_end' => 'نهاية التخفيض (اختياري)', 'show_countdown' => 'إظهار عدّاد تنازلي حتى نهاية التخفيض', 'sale_off' => 'التخفيض متوقف',
  'buy_mode_default' => 'الإجراء الافتراضي لزر المنتج', 'wa_number' => 'رقم واتساب للطلبات (رمز الدولة + الرقم، أرقام فقط؛ الفارغ = أول رابط واتساب)', 'wa_msg_en' => 'رسالة الطلب (إنجليزي)', 'wa_msg_ar' => 'رسالة الطلب (عربي)', 'wa_msg_hint' => 'استخدم {name} و {price} و {shop} داخل الرسالة.',
  'sec_pwa' => 'إضافة إلى الشاشة الرئيسية (تثبيت كتطبيق)', 'pwa_enabled' => 'السماح للزوار بتثبيت الصفحة كتطبيق', 'pwa_install' => 'إظهار زر «أضف إلى الشاشة الرئيسية» في الصفحة', 'pwa_color' => 'لون التطبيق (شريط العنوان وشاشة البداية)', 'pwa_short_en' => 'اسم قصير للتطبيق تحت الأيقونة (إنجليزي، اختياري)', 'pwa_short_ar' => 'اسم قصير للتطبيق تحت الأيقونة (عربي، اختياري)',
  'reviews' => 'التقييمات', 'reviews_enabled' => 'إظهار قسم آراء العملاء', 'reviews_submit' => 'السماح للزوار بكتابة تقييم (يظهر بعد موافقتك)', 'reviews_limit' => 'عدد التقييمات المعروضة', 'rv_add' => 'إضافة تقييم', 'rv_edit' => 'تعديل تقييم', 'rv_name' => 'اسم العميل', 'rv_rating' => 'التقييم (من 1 إلى 5)', 'rv_text' => 'نص التقييم', 'rv_pending' => 'بانتظار الموافقة', 'rv_approved' => 'معتمد', 'rv_approve' => 'موافقة', 'rv_hide' => 'إعادة إلى الانتظار', 'rv_added' => 'تمت إضافة التقييم.', 'rv_updated' => 'تم تحديث التقييم.', 'rv_deleted' => 'تم حذف التقييم.', 'rv_none' => 'لا شيء هنا بعد.', 'confirm_del_rv' => 'حذف هذا التقييم؟',
  'messages' => 'الرسائل', 'contact_enabled' => 'إظهار نموذج التواصل / الطلب في الصفحة', 'contact_title_en' => 'عنوان النموذج (إنجليزي)', 'contact_title_ar' => 'عنوان النموذج (عربي)', 'notify_enabled' => 'أرسل لي بريداً عند كل رسالة جديدة', 'notify_email' => 'البريد الإلكتروني للتنبيهات',
  'notify_note' => 'تنبيهات البريد تستخدم دالة البريد في الخادم. بعض الاستضافات المجانية (ومنها InfinityFree) تمنعها، لذلك راجع هذا الصندوق أيضاً: الشريط الجانبي يعرض رقماً أحمر للرسائل غير المقروءة.', 'msg_inbox' => 'الوارد', 'msg_from' => 'من', 'msg_product' => 'المنتج', 'mark_read' => 'تعليم كمقروءة', 'mark_unread' => 'تعليم كغير مقروءة', 'msg_deleted' => 'تم حذف الرسالة.', 'confirm_del_msg' => 'حذف هذه الرسالة؟', 'no_messages' => 'لا توجد رسائل بعد.', 'reply_wa' => 'واتساب', 'reply_call' => 'اتصال', 'reply_mail' => 'بريد',
  'seo' => 'تحسين محركات البحث', 'seo_title_en' => 'عنوان الصفحة (إنجليزي)', 'seo_title_ar' => 'عنوان الصفحة (عربي)', 'seo_desc_en' => 'وصف الصفحة (إنجليزي)', 'seo_desc_ar' => 'وصف الصفحة (عربي)', 'seo_index' => 'السماح لمحركات البحث (Google) بإدراج هذه الصفحة',
  'seo_intro' => 'العنوان والوصف هما ما تعرضه Google في نتائج البحث (العنوان نحو 60 حرفاً والوصف نحو 155). بيانات النشاط (الاسم والموقع والهاتف ومواعيد العمل) وروابط اللغات تُضاف تلقائياً.', 'k_order' => '(طلب واتساب)',
  'search_ph' => 'ابحث عن منتج…', 'cat_all' => 'الكل', 'no_results' => 'لا توجد منتجات', 'sale_ends_in' => 'ينتهي العرض بعد', 'cd_d' => 'يوم', 'reviews_title' => 'آراء العملاء', 'write_review' => 'اكتب تقييمك', 'your_name' => 'اسمك', 'your_review' => 'رأيك', 'rating_label' => 'التقييم', 'send' => 'إرسال', 'review_thanks' => 'شكراً لك! سيظهر تقييمك بعد الموافقة عليه.', 'review_err' => 'تعذر الإرسال، حاول مرة أخرى.', 'contact_title' => 'تواصل معنا', 'c_name' => 'الاسم', 'c_phone' => 'الجوال / واتساب', 'c_email' => 'البريد الإلكتروني (اختياري)', 'c_product' => 'المنتج (اختياري)', 'c_product_none' => '— اختر منتجاً —', 'c_message' => 'الرسالة', 'c_send' => 'إرسال', 'c_thanks' => 'شكراً لك! وصلتنا رسالتك وسنتواصل معك قريباً.', 'c_err' => 'تعذر الإرسال، حاول مرة أخرى.', 'c_need_contact' => 'أدخل رقم الجوال أو البريد الإلكتروني.', 'pwa_add' => 'أضف إلى الشاشة الرئيسية', 'pwa_ios' => 'اضغط زر المشاركة ثم اختر «إضافة إلى الشاشة الرئيسية».', 'rate_err' => 'محاولات كثيرة، حاول لاحقاً.',
  'lang1_label' => 'اللغة 1 (أول لغة في الزوج)', 'lang2_label' => 'اللغة 2 (ثاني لغة في الزوج)', 'default_lang_label' => 'اللغة التي تظهر أولاً للزوار الجدد', 'dl_first' => 'اللغة 1', 'dl_second' => 'اللغة 2',
  'lang_note' => 'اختر أي لغتين (الافتراضي: الإنجليزية / العربية). يُستخدم الزوج في الصفحة العامة وفي لوحة الإدارة هذه: أزرار اللغة وعناوين خانات النصوص تتبعه. النصوص التي تكتبها في خانات اللغة 1 / اللغة 2 تظهر بما يقابلها من لغة، لذلك بعد تغيير الزوج أعد كتابة تلك الخانات. قوائم الإدارة مترجمة بالكامل للإنجليزية والعربية فقط؛ وفي اللغات الأخرى يبقى نص القوائم بالإنجليزية.',
  'if_intro' => 'هذه هي النصوص الثابتة في الصفحة العامة (العناوين، أزرار الخريطة، الأيام، الشارات) للغتيك: %s. توجد ترجمات جاهزة لبعض اللغات؛ املأ أي خانة أو صحّحها. الخانة الفارغة تستخدم النص الجاهز أو الإنجليزي.', 'english_text' => 'النص الإنجليزي',
  'tl_add' => 'إضافة رابط تتبع', 'tl_edit' => 'تعديل رابط التتبع', 'tl_name' => 'اسم الرابط (لك فقط)', 'tl_visits' => 'الزيارات', 'tl_saved' => 'تم حفظ رابط التتبع.', 'tl_deleted' => 'تم حذف رابط التتبع.', 'tl_confirm_del' => 'حذف رابط التتبع هذا؟ الإحصائيات السابقة تبقى محفوظة.', 'tl_need_source' => 'اسم المصدر مطلوب.',
  'tl_quick' => 'بداية سريعة (اضغط لتعبئة النموذج)', 'tl_none' => 'لا توجد روابط تتبع بعد.', 'k_map' => '(خريطة)', 'map_open_label' => 'الخريطة: فتح في خرائط Google', 'map_dir_label' => 'الخريطة: الاتجاهات',
  'n_stats' => 'الإحصائيات',
  'interface_texts' => 'نصوص الواجهة', 'if_saved' => 'تم حفظ نصوص الواجهة.', 'sz_xl' => 'كبير جداً', 'sz_xxl' => 'ضخم',
  'site_langs' => 'لغات الموقع', 'lang_ar' => 'العربية', 'lang_bn' => 'البنغالية (বাংলা)', 'lang_hi' => 'الهندية (हिन्दी)',
  'map_kicker_en' => 'عنوان صغير فوق الاسم في صندوق الخريطة (إنجليزي، الفارغ = "Our location")', 'map_kicker_ar' => 'عنوان صغير فوق الاسم في صندوق الخريطة (عربي، الفارغ = "موقعنا")',
  'regular_price' => 'السعر العادي', 'sale_price' => 'سعر التخفيض (اختياري)', 'currency' => 'العملة', 'cur_default' => 'العملة الافتراضية (تستخدمها المنتجات التي لا تحدد عملة)', 'use_default' => 'استخدام العملة الافتراضية',
  'show_discount' => 'إظهار شارة خصم (مثل -20%) على المنتجات التي لها سعر تخفيض', 'price_text' => 'نص سعر مخصص (اختياري، يُستخدم فقط عند عدم وجود سعر عادي، مثل «مجاني» أو «اسأل عن السعر»)',
  'cur_saved' => 'تم حفظ إعدادات العملة.', 'sale_lower' => 'سعر التخفيض يجب أن يكون أقل من السعر العادي (لم يُحفظ سعر التخفيض).', 'sale_needs_regular' => 'أضف السعر العادي قبل تحديد سعر التخفيض.', 'bad_price' => 'الأسعار يجب أن تكون أرقاماً.', 'sec_prices' => 'الأسعار والعملة', 'was' => 'كان',
  'hours' => 'مواعيد العمل', 'hours_enabled' => 'إظهار قسم مواعيد العمل', 'hours_badge' => 'إظهار شارة «مفتوح الآن / مغلق الآن»', 'hours_title_en' => 'عنوان القسم (إنجليزي)', 'hours_title_ar' => 'عنوان القسم (عربي)',
  'hours_pos' => 'الموضع (مكان الظهور)', 'hours_fmt' => 'صيغة الوقت', 'fmt_12' => '12 ساعة (ص / م)', 'fmt_24' => '24 ساعة', 'hours_start' => 'أول يوم يظهر',
  'closed' => 'مغلق', 'all_day' => 'مفتوح 24 ساعة', 'open_24h' => 'مفتوح 24 ساعة', 'shift1' => 'الفترة 1', 'shift2' => 'الفترة 2 (اختياري)', 'opens' => 'الفتح', 'closes' => 'الإغلاق', 'open_now' => 'مفتوح الآن', 'closed_now' => 'مغلق الآن', 'day' => 'اليوم',
  'hours_saved' => 'تم حفظ مواعيد العمل.', 'hours_note' => 'اترك الوقت فارغاً لتجاوزه. استخدم الفترة 2 عندما يغلق المحل لاستراحة ثم يفتح من جديد. وقت الإغلاق الأبكر من وقت الفتح يعني الإغلاق بعد منتصف الليل.',
  'day_0' => 'الأحد', 'day_1' => 'الاثنين', 'day_2' => 'الثلاثاء', 'day_3' => 'الأربعاء', 'day_4' => 'الخميس', 'day_5' => 'الجمعة', 'day_6' => 'السبت',
  'price_hint' => 'اكتب المبلغ ثم SAR (مثال: 25 SAR). يظهر مع رمز الريال السعودي الجديد.',
  'map_embed' => 'كود تضمين خرائط Google (الخيار الأفضل)', 'map_embed_hint' => 'في خرائط Google افتح مكانك > مشاركة > تضمين خريطة > نسخ HTML ثم الصقه هنا. سيرى الزوار مكانك الحقيقي في Google، وعند الضغط على الدبوس تظهر تفاصيله. إذا تُرك فارغاً تُستخدم الإحداثيات أعلاه.', 'bad_embed' => 'هذا ليس كود تضمين خرائط Google.',
  'stat_visits' => 'الزيارات', 'stat_unique' => 'زوار مختلفون', 'stat_clicks' => 'النقرات', 'visits_over_time' => 'الزيارات عبر الزمن',
  'src_email' => 'البريد الإلكتروني', 'campaigns' => 'الحملات (UTM)', 'no_campaign' => 'لا توجد حملات متتبعة بعد', 'per_page' => 'عدد الصفوف في الصفحة', 'page_x_of' => 'صفحة %d من %d', 'medium' => 'الوسيلة', 'campaign_name' => 'اسم الحملة', 'utm_source_name' => 'اسم المصدر (مثال: whatsapp)',
  'utm_intro' => 'كل رابط في الأسفل يحمل وسوم UTM (‏utm_source و utm_medium و utm_campaign). شارك هذه الروابط (وليس رابط الموقع العادي) في واتساب وفيسبوك وإنستغرام والبريد ورموز QR، وستظهر الإحصائيات مصدر كل زائر بدقة.',
  'utm_why' => 'السبب: تطبيقات المحادثة مثل واتساب تخفي مصدر النقرة، فيُحسب الرابط العادي «مباشر». الرابط الذي يحمل وسوم UTM يُعرف دائماً.', 'qr_fail' => 'تعذر تحميل مكتبة QR. تأكد من الإنترنت وأعد تحميل الصفحة.',
  'med_social' => 'وسائل التواصل', 'med_qr' => 'رمز QR', 'med_message' => 'رسالة / محادثة', 'med_email' => 'بريد إلكتروني', 'med_referral' => 'إحالة', 'med_banner' => 'بانر / إعلان', 'med_other' => 'أخرى',
  'images_multi' => 'ارفع الصور (يمكنك اختيار عدة صور مرة واحدة)', 'image_urls' => 'أو الصق روابط الصور (رابط في كل سطر)', 'slides_added' => 'تمت إضافة %d شريحة.',
  'sec_buy' => 'زر المنتج (اشتري الآن / اطلب الآن)', 'buy_enabled' => 'إظهار زر على كل منتج', 'buy_text_en' => 'نص الزر الافتراضي (إنجليزي)', 'buy_text_ar' => 'نص الزر الافتراضي (عربي)', 'buy_bg' => 'لون خلفية زر المنتج', 'buy_fg' => 'لون نص زر المنتج',
  'pbtn_en' => 'نص الزر لهذا المنتج (إنجليزي، اختياري)', 'pbtn_ar' => 'نص الزر لهذا المنتج (عربي، اختياري)', 'pbtn_hint' => 'اتركه فارغاً لاستخدام النص الافتراضي من المظهر (مثل «اشتري الآن» أو «اطلب الآن»). الزر يفتح رابط المنتج أعلاه.',
  'banners' => 'البانرات', 'bn_intro' => 'أنشئ سلايدر، واختر مكان ظهوره في الصفحة، ثم أضف الشرائح (صور مع روابط اختيارية). إذا كان هناك عنوان في نفس الموضع يظهر العنوان أولاً ثم السلايدر تحته.',
  'add_slider' => 'إضافة سلايدر بانر', 'edit_slider' => 'تعديل سلايدر', 'slider_name' => 'اسم السلايدر (لك فقط)', 'slider_position' => 'الموضع (مكان الظهور)', 'bpos_top' => 'أعلى الصفحة (فوق الغلاف)',
  'autoplay' => 'تشغيل تلقائي', 'interval' => 'ثواني لكل شريحة', 'ratio' => 'شكل البانر', 'r_3_1' => 'شريط عريض (3:1)', 'r_2_1' => 'عريض (2:1)', 'r_16_9' => 'شكل فيديو (16:9)', 'r_4_3' => 'كلاسيكي (4:3)', 'r_1_1' => 'مربع (1:1)',
  'fit' => 'ملاءمة الصورة', 'fit_cover' => 'ملء البانر (قص الأطراف)', 'fit_contain' => 'إظهار الصورة كاملة', 'show_arrows' => 'إظهار الأسهم', 'show_dots' => 'إظهار النقاط',
  'manage_slides' => 'إدارة الشرائح', 'slides_count' => 'الشرائح', 'slider_added' => 'تمت إضافة السلايدر.', 'slider_updated' => 'تم تحديث السلايدر.', 'slider_deleted' => 'تم حذف السلايدر.', 'confirm_del_slider' => 'حذف هذا السلايدر وكل شرائحه؟',
  'slides' => 'الشرائح', 'add_slide' => 'إضافة شريحة', 'edit_slide' => 'تعديل شريحة', 'slide_image' => 'صورة الشريحة', 'caption_en' => 'تعليق (إنجليزي، اختياري)', 'caption_ar' => 'تعليق (عربي، اختياري)', 'slide_link' => 'الرابط عند الضغط (اختياري)',
  'slide_added' => 'تمت إضافة الشريحة.', 'slide_updated' => 'تم تحديث الشريحة.', 'slide_deleted' => 'تم حذف الشريحة.', 'confirm_del_slide' => 'حذف هذه الشريحة؟', 'need_image' => 'ارفع صورة أو الصق رابط صورة.', 'back_banners' => 'العودة إلى البانرات',
  'no_slides' => 'لا توجد شرائح بعد.', 'k_banner' => '(بانر)', 'prev' => 'السابق', 'next' => 'التالي', 'slider' => 'السلايدر',
  'headings' => 'العناوين', 'add_heading' => 'إضافة عنوان', 'edit_heading' => 'تعديل عنوان', 'position' => 'الموضع (مكان الظهور)',
  'pos_top_bar' => 'الشريط العلوي (شريط ملون فوق الغلاف)', 'pos_after_profile' => 'تحت اسم المتجر والوصف', 'pos_before_links' => 'فوق أزرار الروابط', 'pos_before_products' => 'فوق المنتجات', 'pos_before_map' => 'فوق الخريطة', 'pos_bottom' => 'الأسفل (فوق التذييل)',
  'hd_text_en' => 'نص العنوان (إنجليزي)', 'hd_text_ar' => 'نص العنوان (عربي)', 'hd_sub_en' => 'نص صغير تحته (إنجليزي، اختياري)', 'hd_sub_ar' => 'نص صغير تحته (عربي، اختياري)', 'hd_link' => 'رابط (اختياري)',
  'align' => 'المحاذاة', 'al_start' => 'البداية', 'al_center' => 'الوسط', 'al_end' => 'النهاية', 'size' => 'الحجم', 'sz_sm' => 'صغير', 'sz_md' => 'متوسط', 'sz_lg' => 'كبير',
  'heading_added' => 'تمت إضافة العنوان.', 'heading_updated' => 'تم تحديث العنوان.', 'heading_deleted' => 'تم حذف العنوان.', 'confirm_del_heading' => 'حذف هذا العنوان؟',
  'hd_intro' => 'أضف ما تشاء من العناوين واختر مكان ظهور كل واحد. العناوين في نفس المكان تظهر حسب رقم الترتيب. عناوين الشريط العلوي تستخدم الألوان المحددة في المظهر ما لم تعطها ألواناً خاصة.',
  'sec_topbar' => 'شكل الشريط العلوي', 'admin_preloader' => 'إظهار شاشة التحميل أيضاً في لوحة التحكم (كل صفحات الإدارة)', 'text' => 'النص',
  'sec_footer' => 'التذييل (يظهر في كل الصفحات)', 'footer_enabled' => 'إظهار التذييل', 'footer_text_en' => 'نص التذييل (إنجليزي)', 'footer_text_ar' => 'نص التذييل (عربي)', 'footer_link_en' => 'نص رابط التذييل (إنجليزي)', 'footer_link_ar' => 'نص رابط التذييل (عربي)', 'footer_url' => 'رابط التذييل',
  'sec_headings' => 'عناوين الأقسام', 'hd_links' => 'عنوان فوق أزرار الروابط', 'hd_products' => 'عنوان فوق المنتجات', 'hd_map' => 'عنوان فوق الخريطة', 'hd_show' => 'إظهار هذا العنوان', 'hd_en' => 'نص العنوان (إنجليزي)', 'hd_ar' => 'نص العنوان (عربي)', 'hd_hint' => 'مثال: «جميع المنتجات». أزل العلامة لإخفاء العنوان.',
  'map_explore' => 'تحريك الخريطة',
  'appearance' => 'المظهر', 'sec_buttons' => 'ألوان أزرار الروابط', 'btn_mode' => 'نمط ألوان الأزرار', 'btn_mode_brand' => 'ألوان الشعارات (لكل نوع زر لونه)', 'btn_mode_custom' => 'لون واحد مخصص لكل الأزرار',
  'btn_bg' => 'لون خلفية الزر', 'btn_text' => 'لون نص الزر', 'custom_colors' => 'ألوان مخصصة لهذا الزر', 'bg_color' => 'لون الخلفية', 'text_color' => 'لون النص',
  'sec_headline' => 'شريط العنوان (أعلى الصفحة)', 'headline_enabled' => 'إظهار شريط العنوان', 'headline_en' => 'نص العنوان (إنجليزي)', 'headline_ar' => 'نص العنوان (عربي)', 'headline_url' => 'رابط العنوان (اختياري)', 'headline_bg' => 'لون خلفية العنوان', 'headline_fg' => 'لون نص العنوان', 'headline_scroll' => 'نص متحرك',
  'sec_preloader' => 'شاشة التحميل', 'preloader_enabled' => 'إظهار شاشة التحميل (حركة الشعار) أثناء تحميل الصفحة',
  'sec_popup' => 'نافذة الترحيب', 'popup_enabled' => 'إظهار نافذة الترحيب', 'popup_title_en' => 'عنوان النافذة (إنجليزي)', 'popup_title_ar' => 'عنوان النافذة (عربي)', 'popup_text_en' => 'رسالة النافذة (إنجليزي)', 'popup_text_ar' => 'رسالة النافذة (عربي)',
  'popup_btn_en' => 'نص زر النافذة (إنجليزي)', 'popup_btn_ar' => 'نص زر النافذة (عربي)', 'popup_btn_url' => 'رابط زر النافذة (اختياري، فارغ = يغلق فقط)', 'popup_freq' => 'عدد مرات الظهور', 'pf_always' => 'كل زيارة (كل تحميل للصفحة)', 'pf_session' => 'مرة في كل جلسة متصفح', 'pf_day' => 'مرة في اليوم', 'pf_once' => 'مرة واحدة فقط',
  'popup_delay' => 'الظهور بعد (ثوانٍ)', 'close' => 'إغلاق', 'appearance_saved' => 'تم حفظ المظهر.',
  'tracking' => 'روابط QR والتتبع', 'live_visitors' => 'الزوار الآن', 'unknown' => 'غير معروف', 'range_7' => 'آخر 7 أيام', 'range_30' => 'آخر 30 يوماً', 'range_all' => 'كل الفترة', 'sources' => 'مصادر الزيارات',
  'src_qr' => 'رمز QR', 'src_search' => 'محرك بحث', 'src_social' => 'وسائل التواصل', 'src_referral' => 'موقع آخر', 'src_campaign' => 'رابط تتبع', 'src_direct' => 'مباشر / غير معروف',
  'src_detail' => 'تفاصيل المصدر', 'countries' => 'الدول', 'cities' => 'المدن / المناطق', 'visitor_location' => 'الموقع', 'no_data' => 'لا توجد بيانات بعد', 'visits_col' => 'الزيارات',
  'geo_note' => 'الموقع تقريبي (من عنوان IP): الدولة والمدينة/المنطقة. لا يمكنه تحديد الحي بدقة، وشبكات الجوال تُظهر غالباً أقرب مدينة كبيرة.',
  'src_note' => 'الزوار الذين يفتحون الرابط من تطبيقات تخفي المصدر (مثل محادثات واتساب وسناب) يظهرون كـ «مباشر». استخدم روابط التتبع لقياسهم.',
  'qr_intro' => 'استخدم هذه الروابط في رموز QR ومنشوراتك، وستظهر الإحصائيات مصدر الزوار بدقة.', 'qr_custom' => 'اسم مصدر مخصص (حروف إنجليزية وأرقام و - أو _)', 'qr_make' => 'إنشاء',
  'qr_download' => 'تحميل QR', 'qr_copy' => 'نسخ الرابط', 'qr_copied' => 'تم النسخ', 'qr_main' => 'رمز QR (المنشورات، القائمة، باب المحل)',
  'favicon' => 'أيقونة الموقع Favicon (الأيقونة الصغيرة في تبويب المتصفح)', 'og_image' => 'صورة معاينة الرابط (تظهر عند مشاركة الرابط)', 'og_hint' => 'المقاس المقترح 1200x630. إن تُركت فارغة تُستخدم صورة الغلاف.', 'fav_hint' => 'صورة PNG مربعة 192x192 على الأقل. إن تُركت فارغة تُستخدم الصورة الشخصية.',
  'directions' => 'الاتجاهات', 'developed_by' => 'تطوير', 'links_layout' => 'شكل الأزرار', 'layout_list' => 'قائمة (زر في كل سطر)', 'layout_grid' => 'شبكة (زران في السطر)', 'lay_list' => 'عرض قائمة', 'lay_grid' => 'عرض شبكة',
  'icon_removed' => 'تمت إزالة الأيقونة المخصصة.', 'image_removed' => 'تمت إزالة الصورة.',
  'admins' => 'المشرفون', 'add_admin' => 'إضافة مشرف', 'edit_admin' => 'تعديل مشرف', 'full_name' => 'الاسم', 'email' => 'البريد الإلكتروني', 'phone' => 'الجوال', 'login_id' => 'اسم المستخدم أو البريد أو الجوال',
  'pin_hint' => 'كلمة المرور: أرقام فقط من 4 إلى 6 أرقام.', 'pin_invalid' => 'كلمة المرور أرقام فقط من 4 إلى 6 أرقام.', 'new_pw' => 'كلمة المرور الجديدة (4-6 أرقام)', 'pw_short' => 'كلمة المرور أرقام فقط من 4 إلى 6 أرقام.',
  'admin_added' => 'تمت إضافة المشرف.', 'admin_updated' => 'تم تحديث المشرف.', 'admin_deleted' => 'تم حذف المشرف.', 'confirm_del_admin' => 'حذف هذا المشرف؟', 'cant_delete_self' => 'لا يمكنك حذف حسابك.', 'cant_delete_last' => 'لا يمكن حذف آخر مشرف.',
  'dup_admin' => 'اسم المستخدم أو البريد أو الجوال مستخدم مسبقاً.', 'need_identifier' => 'أدخل اسم مستخدم أو بريداً أو جوالاً على الأقل.', 'locked' => 'محاولات كثيرة. حاول بعد 10 دقائق.', 'leave_blank' => 'اتركها فارغة للإبقاء على كلمة المرور الحالية', 'you' => '(أنت)',
  'online_now' => 'متصل الآن', 'map_coords' => 'إحداثيات الموقع (خط العرض، خط الطول)', 'map_coords_hint' => 'في خرائط Google: اضغط مطولاً على مكانك ثم انسخ الأرقام الظاهرة (مثال: 25.8694, 43.4973).', 'bad_coords' => 'الإحداثيات غير صحيحة.',
  'map_name_en' => 'اسم الموقع على الخريطة (إنجليزي)', 'map_name_ar' => 'اسم الموقع على الخريطة (عربي)', 'image_url' => 'أو الصق رابط الصورة', 'remove_image' => 'إزالة الصورة', 'bad_url' => 'رابط الصورة يجب أن يبدأ بـ http:// أو https://',
  'custom_icon' => 'أيقونة مخصصة (اختياري، PNG/JPG/WEBP)', 'remove_icon' => 'إزالة الأيقونة المخصصة', 'password_label' => 'كلمة المرور', 'name_col' => 'الاسم',
 ],

 'bn' => [
  'products' => 'আমাদের পণ্য', 'all_products' => 'সব পণ্য দেখুন', 'location' => 'আমাদের অবস্থান', 'open_maps' => 'গুগল ম্যাপে খুলুন', 'directions' => 'দিকনির্দেশনা', 'map_title' => 'মানচিত্র', 'map_explore' => 'মানচিত্র ঘুরে দেখুন',
  'links_layout' => 'বোতামের বিন্যাস', 'lay_list' => 'তালিকা দেখুন', 'lay_grid' => 'গ্রিড দেখুন', 'close' => 'বন্ধ করুন', 'prev' => 'আগের', 'next' => 'পরের',
  'open_now' => 'এখন খোলা', 'closed_now' => 'এখন বন্ধ', 'closed' => 'বন্ধ', 'open_24h' => '২৪ ঘণ্টা খোলা', 'developed_by' => 'তৈরি করেছেন',
  'day_0' => 'রবিবার', 'day_1' => 'সোমবার', 'day_2' => 'মঙ্গলবার', 'day_3' => 'বুধবার', 'day_4' => 'বৃহস্পতিবার', 'day_5' => 'শুক্রবার', 'day_6' => 'শনিবার',
  'search_ph' => 'পণ্য খুঁজুন…', 'cat_all' => 'সব', 'no_results' => 'কোনো পণ্য পাওয়া যায়নি', 'sale_ends_in' => 'অফার শেষ হবে', 'cd_d' => 'দিন', 'reviews_title' => 'গ্রাহকদের মতামত', 'write_review' => 'মতামত লিখুন', 'your_name' => 'আপনার নাম', 'your_review' => 'আপনার মতামত', 'rating_label' => 'রেটিং', 'send' => 'পাঠান', 'review_thanks' => 'ধন্যবাদ! অনুমোদনের পর আপনার মতামত দেখানো হবে।', 'review_err' => 'পাঠানো যায়নি, আবার চেষ্টা করুন।', 'contact_title' => 'যোগাযোগ করুন', 'c_name' => 'আপনার নাম', 'c_phone' => 'ফোন / হোয়াটসঅ্যাপ', 'c_email' => 'ইমেইল (ঐচ্ছিক)', 'c_product' => 'পণ্য (ঐচ্ছিক)', 'c_product_none' => '— পণ্য নির্বাচন করুন —', 'c_message' => 'বার্তা', 'c_send' => 'পাঠান', 'c_thanks' => 'ধন্যবাদ! আপনার বার্তা পাঠানো হয়েছে, শীঘ্রই যোগাযোগ করব।', 'c_err' => 'পাঠানো যায়নি, আবার চেষ্টা করুন।', 'c_need_contact' => 'ফোন বা ইমেইল দিন।', 'pwa_add' => 'হোম স্ক্রিনে যোগ করুন', 'pwa_ios' => 'শেয়ার বোতাম চাপুন, তারপর “Add to Home Screen” বেছে নিন।', 'rate_err' => 'অনেক বেশি চেষ্টা হয়েছে, পরে আবার চেষ্টা করুন।',
 ],
 'hi' => [
  'products' => 'हमारे उत्पाद', 'all_products' => 'सभी उत्पाद देखें', 'location' => 'हमारा स्थान', 'open_maps' => 'गूगल मैप्स में खोलें', 'directions' => 'दिशा-निर्देश', 'map_title' => 'नक्शा', 'map_explore' => 'नक्शा देखें',
  'links_layout' => 'बटन लेआउट', 'lay_list' => 'सूची दृश्य', 'lay_grid' => 'ग्रिड दृश्य', 'close' => 'बंद करें', 'prev' => 'पिछला', 'next' => 'अगला',
  'open_now' => 'अभी खुला है', 'closed_now' => 'अभी बंद है', 'closed' => 'बंद', 'open_24h' => '24 घंटे खुला', 'developed_by' => 'विकसित किया',
  'day_0' => 'रविवार', 'day_1' => 'सोमवार', 'day_2' => 'मंगलवार', 'day_3' => 'बुधवार', 'day_4' => 'गुरुवार', 'day_5' => 'शुक्रवार', 'day_6' => 'शनिवार',
  'search_ph' => 'उत्पाद खोजें…', 'cat_all' => 'सभी', 'no_results' => 'कोई उत्पाद नहीं मिला', 'sale_ends_in' => 'ऑफ़र समाप्त होने में', 'cd_d' => 'दिन', 'reviews_title' => 'ग्राहकों की राय', 'write_review' => 'राय लिखें', 'your_name' => 'आपका नाम', 'your_review' => 'आपकी राय', 'rating_label' => 'रेटिंग', 'send' => 'भेजें', 'review_thanks' => 'धन्यवाद! मंज़ूरी के बाद आपकी राय दिखाई जाएगी।', 'review_err' => 'भेजा नहीं जा सका, फिर से कोशिश करें।', 'contact_title' => 'संपर्क करें', 'c_name' => 'आपका नाम', 'c_phone' => 'फ़ोन / व्हाट्सऐप', 'c_email' => 'ईमेल (वैकल्पिक)', 'c_product' => 'उत्पाद (वैकल्पिक)', 'c_product_none' => '— उत्पाद चुनें —', 'c_message' => 'संदेश', 'c_send' => 'भेजें', 'c_thanks' => 'धन्यवाद! आपका संदेश भेज दिया गया है, हम जल्द संपर्क करेंगे।', 'c_err' => 'भेजा नहीं जा सका, फिर से कोशिश करें।', 'c_need_contact' => 'कृपया फ़ोन या ईमेल दें।', 'pwa_add' => 'होम स्क्रीन पर जोड़ें', 'pwa_ios' => 'शेयर बटन दबाएँ, फिर “Add to Home Screen” चुनें।', 'rate_err' => 'बहुत अधिक प्रयास, बाद में फिर कोशिश करें।',
 ],
 'ur' => [
  'products' => 'ہمارے مصنوعات', 'all_products' => 'تمام مصنوعات دیکھیں', 'location' => 'ہمارا مقام', 'open_maps' => 'گوگل میپس میں کھولیں', 'directions' => 'راستہ', 'map_title' => 'نقشہ', 'map_explore' => 'نقشہ دیکھیں', 'links_layout' => 'بٹن کی ترتیب', 'lay_list' => 'فہرست کا منظر', 'lay_grid' => 'گرڈ کا منظر', 'close' => 'بند کریں', 'prev' => 'پچھلا', 'next' => 'اگلا', 'open_now' => 'ابھی کھلا ہے', 'closed_now' => 'ابھی بند ہے', 'closed' => 'بند', 'open_24h' => '24 گھنٹے کھلا', 'developed_by' => 'تیار کردہ', 'day_0' => 'اتوار', 'day_1' => 'پیر', 'day_2' => 'منگل', 'day_3' => 'بدھ', 'day_4' => 'جمعرات', 'day_5' => 'جمعہ', 'day_6' => 'ہفتہ',
 ],
 'tr' => [
  'products' => 'Ürünlerimiz', 'all_products' => 'Tüm ürünleri gör', 'location' => 'Konumumuz', 'open_maps' => 'Google Haritalar\'da aç', 'directions' => 'Yol tarifi', 'map_title' => 'Harita', 'map_explore' => 'Haritayı keşfet', 'links_layout' => 'Düğme düzeni', 'lay_list' => 'Liste görünümü', 'lay_grid' => 'Izgara görünümü', 'close' => 'Kapat', 'prev' => 'Önceki', 'next' => 'Sonraki', 'open_now' => 'Şu an açık', 'closed_now' => 'Şu an kapalı', 'closed' => 'Kapalı', 'open_24h' => '24 saat açık', 'developed_by' => 'Geliştiren', 'day_0' => 'Pazar', 'day_1' => 'Pazartesi', 'day_2' => 'Salı', 'day_3' => 'Çarşamba', 'day_4' => 'Perşembe', 'day_5' => 'Cuma', 'day_6' => 'Cumartesi',
 ],
 'fr' => [
  'products' => 'Nos produits', 'all_products' => 'Voir tous les produits', 'location' => 'Notre emplacement', 'open_maps' => 'Ouvrir dans Google Maps', 'directions' => 'Itinéraire', 'map_title' => 'Carte', 'map_explore' => 'Explorer la carte', 'links_layout' => 'Disposition des boutons', 'lay_list' => 'Vue en liste', 'lay_grid' => 'Vue en grille', 'close' => 'Fermer', 'prev' => 'Précédent', 'next' => 'Suivant', 'open_now' => 'Ouvert maintenant', 'closed_now' => 'Fermé maintenant', 'closed' => 'Fermé', 'open_24h' => 'Ouvert 24h/24', 'developed_by' => 'Développé par', 'day_0' => 'Dimanche', 'day_1' => 'Lundi', 'day_2' => 'Mardi', 'day_3' => 'Mercredi', 'day_4' => 'Jeudi', 'day_5' => 'Vendredi', 'day_6' => 'Samedi',
 ],
 'es' => [
  'products' => 'Nuestros productos', 'all_products' => 'Ver todos los productos', 'location' => 'Nuestra ubicación', 'open_maps' => 'Abrir en Google Maps', 'directions' => 'Cómo llegar', 'map_title' => 'Mapa', 'map_explore' => 'Explorar el mapa', 'links_layout' => 'Disposición de los botones', 'lay_list' => 'Vista de lista', 'lay_grid' => 'Vista de cuadrícula', 'close' => 'Cerrar', 'prev' => 'Anterior', 'next' => 'Siguiente', 'open_now' => 'Abierto ahora', 'closed_now' => 'Cerrado ahora', 'closed' => 'Cerrado', 'open_24h' => 'Abierto 24 horas', 'developed_by' => 'Desarrollado por', 'day_0' => 'Domingo', 'day_1' => 'Lunes', 'day_2' => 'Martes', 'day_3' => 'Miércoles', 'day_4' => 'Jueves', 'day_5' => 'Viernes', 'day_6' => 'Sábado',
 ],
 'id' => [
  'products' => 'Produk kami', 'all_products' => 'Lihat semua produk', 'location' => 'Lokasi kami', 'open_maps' => 'Buka di Google Maps', 'directions' => 'Petunjuk arah', 'map_title' => 'Peta', 'map_explore' => 'Jelajahi peta', 'links_layout' => 'Tata letak tombol', 'lay_list' => 'Tampilan daftar', 'lay_grid' => 'Tampilan kisi', 'close' => 'Tutup', 'prev' => 'Sebelumnya', 'next' => 'Berikutnya', 'open_now' => 'Buka sekarang', 'closed_now' => 'Tutup sekarang', 'closed' => 'Tutup', 'open_24h' => 'Buka 24 jam', 'developed_by' => 'Dikembangkan oleh', 'day_0' => 'Minggu', 'day_1' => 'Senin', 'day_2' => 'Selasa', 'day_3' => 'Rabu', 'day_4' => 'Kamis', 'day_5' => 'Jumat', 'day_6' => 'Sabtu',
 ],
];
function is_admin_ctx() { $sc = $_SERVER['SCRIPT_NAME'] ?? ''; return strpos($sc, '/admin/') !== false || basename($sc) === 'install.php'; }
function lang_valid($c) { return is_string($c) && isset(LANGS[$c]); }
// The language PAIR (chosen in Admin > Shop info). Default: English / Arabic. It is used by the public page AND by the admin panel.
function first_lang() {
  static $c;
  if ($c === null) { $c = 'en'; try { $v = settings()['lang1'] ?? 'en'; if (lang_valid($v)) $c = $v; } catch (Throwable $e) {} }
  return $c;
}
function second_lang() {
  static $c;
  if ($c === null) {
    $c = 'ar';
    try { $s = settings(); $v = $s['lang2'] ?? ($s['second_lang'] ?? 'ar'); if (lang_valid($v)) $c = $v; } catch (Throwable $e) {}
    if ($c === first_lang()) $c = first_lang() === 'ar' ? 'en' : 'ar';
  }
  return $c;
}
function first_dir() { return LANGS[first_lang()]['dir']; }
function second_dir() { return LANGS[second_lang()]['dir']; }
// Language shown first to a new visitor (public site). The admin panel starts in the first language of the pair.
function default_lang() {
  if (is_admin_ctx()) return first_lang();
  $d = 'second'; try { $d = settings()['lang_default'] ?? 'second'; } catch (Throwable $e) {}
  return ($d === 'en' || $d === 'first') ? first_lang() : second_lang();
}
// Current language: one of the pair. Switch with ?lang=xx (remembered in a cookie; the public site and the admin keep separate choices).
function L() {
  static $l;
  if ($l === null) {
    $allowed = [first_lang(), second_lang()]; $cookie = is_admin_ctx() ? 'alang' : 'lang'; $def = default_lang();
    if (isset($_GET['lang']) && in_array($_GET['lang'], $allowed, true)) { setcookie($cookie, $_GET['lang'], time() + 31536000, '/'); $l = $_GET['lang']; }
    else { $c = $_COOKIE[$cookie] ?? $def; $l = in_array($c, $allowed, true) ? $c : $def; }
  }
  return $l;
}
// The two language buttons (public page, admin header, login)
function lang_switch_html() {
  $h = '';
  foreach ([first_lang(), second_lang()] as $c) $h .= '<a href="' . e(lang_url($c)) . '"' . (L() === $c ? ' class="on" aria-current="true"' : '') . ' hreflang="' . e($c) . '" lang="' . e($c) . '">' . e(LANGS[$c]['label']) . '</a>';
  return $h;
}
function t($k) {
  $l = L();
  if (!is_admin_ctx()) { try { $o = settings()['ui_' . $l . '_' . $k] ?? ''; if ($o !== '') return $o; } catch (Throwable $e) {} }   // texts edited in Admin > Interface texts
  $v = STR[$l][$k] ?? STR['en'][$k] ?? $k;
  if (is_admin_ctx() && (first_lang() !== 'en' || second_lang() !== 'ar') && strpos($k, 'lang_') !== 0 && $k !== 'english_text') {
    $f = LANGS[first_lang()]; $x = LANGS[second_lang()];   // labels such as "(English)" / "(Arabic)" name the languages of the pair
    $v = ($l === 'ar' && isset(STR['ar'][$k])) ? strtr($v, ['إنجليزي' => $f['ar'], 'عربي' => $x['ar']]) : strtr($v, ['English' => $f['en'], 'Arabic' => $x['en']]);
  }
  return $v;
}
function dir_attr() { return (LANGS[L()]['dir'] ?? 'ltr') === 'rtl' ? 'rtl' : 'ltr'; }
// Pick the text for the current language: English fields (*_en) or second-language fields (*_ar). Falls back to the other one if empty.
function pick($row, $f) {
  $a = $row[$f . '_ar'] ?? ''; $e = $row[$f . '_en'] ?? '';
  return L() === first_lang() ? ($e !== '' ? $e : $a) : ($a !== '' ? $a : $e);
}
function lang_url($l) { $q = $_GET; $q['lang'] = $l; return '?' . http_build_query($q); }
