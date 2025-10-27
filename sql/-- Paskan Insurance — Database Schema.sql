-- Paskan Insurance — Database Schema (PostgreSQL)
-- All CREATE TABLE statements with indexes and constraints

-- 1. Users
CREATE TABLE users (
id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
email VARCHAR(255) UNIQUE,
phone VARCHAR(32) UNIQUE,
password_hash VARCHAR(255),
is_email_verified BOOLEAN DEFAULT FALSE,
is_phone_verified BOOLEAN DEFAULT FALSE,
role VARCHAR(32) DEFAULT 'customer',
status VARCHAR(16) DEFAULT 'active',
created_at TIMESTAMPTZ DEFAULT now(),
updated_at TIMESTAMPTZ DEFAULT now()
);

-- 2. User Profiles
CREATE TABLE user_profiles (
user_id UUID PRIMARY KEY REFERENCES users(id) ON DELETE CASCADE,
full_name VARCHAR(255),
dob DATE,
gender VARCHAR(16),
nationality VARCHAR(64),
marital_status VARCHAR(32),
national_id VARCHAR(64),
address JSONB,
language VARCHAR(8) DEFAULT 'en',
metadata JSONB,
created_at TIMESTAMPTZ DEFAULT now(),
updated_at TIMESTAMPTZ DEFAULT now()
);

-- 3. Addresses
CREATE TABLE addresses (
id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
user_id UUID REFERENCES users(id) ON DELETE CASCADE,
label VARCHAR(32),
address JSONB,
is_primary BOOLEAN DEFAULT FALSE,
created_at TIMESTAMPTZ DEFAULT now()
);

-- 4. Insurance Categories
CREATE TABLE insurance_categories (
id SERIAL PRIMARY KEY,
slug VARCHAR(64) UNIQUE,
name VARCHAR(128),
description TEXT,
is_enabled BOOLEAN DEFAULT TRUE,
created_at TIMESTAMPTZ DEFAULT now()
);

-- 5. Insurers
CREATE TABLE insurers (
id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
name VARCHAR(255) NOT NULL,
code VARCHAR(64) UNIQUE,
logo_url TEXT,
website TEXT,
contact_json JSONB,
is_active BOOLEAN DEFAULT TRUE,
created_at TIMESTAMPTZ DEFAULT now(),
updated_at TIMESTAMPTZ DEFAULT now()
);

-- 6. Insurer APIs
CREATE TABLE insurer_apis (
id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
insurer_id UUID REFERENCES insurers(id) ON DELETE CASCADE,
name VARCHAR(128),
api_type VARCHAR(64),
base_url TEXT,
auth JSONB,
enabled BOOLEAN DEFAULT TRUE,
rate_limit_per_min INT DEFAULT 60,
last_health_check TIMESTAMPTZ,
metadata JSONB,
created_at TIMESTAMPTZ DEFAULT now()
);

-- 7. Products
CREATE TABLE products (
id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
insurer_id UUID REFERENCES insurers(id),
category_id INT REFERENCES insurance_categories(id),
insurer_product_code VARCHAR(128),
name VARCHAR(255),
short_description TEXT,
long_description TEXT,
sum_insured NUMERIC,
min_premium NUMERIC,
max_premium NUMERIC,
currency VARCHAR(8) DEFAULT 'THB',
is_active BOOLEAN DEFAULT TRUE,
display_order INT DEFAULT 0,
metadata JSONB,
created_at TIMESTAMPTZ DEFAULT now()
);

-- 8. Product Features
CREATE TABLE product_features (
id SERIAL PRIMARY KEY,
product_id UUID REFERENCES products(id) ON DELETE CASCADE,
key VARCHAR(128),
value TEXT,
is_highlight BOOLEAN DEFAULT FALSE
);

-- 9. Product Addons
CREATE TABLE product_addons (
id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
product_id UUID REFERENCES products(id) ON DELETE CASCADE,
name VARCHAR(255),
description TEXT,
price NUMERIC,
is_mandatory BOOLEAN DEFAULT FALSE
);

-- 10. Quotes
CREATE TABLE quotes (
id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
user_id UUID REFERENCES users(id),
category_id INT REFERENCES insurance_categories(id),
search_payload JSONB,
status VARCHAR(32) DEFAULT 'completed',
created_at TIMESTAMPTZ DEFAULT now()
);

-- 11. Quote Items
CREATE TABLE quote_items (
id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
quote_id UUID REFERENCES quotes(id) ON DELETE CASCADE,
insurer_id UUID REFERENCES insurers(id),
product_id UUID REFERENCES products(id),
response_payload JSONB,
premium NUMERIC,
premium_without_markup NUMERIC,
currency VARCHAR(8) DEFAULT 'THB',
coverage JSONB,
available_addons JSONB,
external_reference VARCHAR(255),
created_at TIMESTAMPTZ DEFAULT now(),
ttl TIMESTAMPTZ
);

-- 12. Policies
CREATE TABLE policies (
id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
user_id UUID REFERENCES users(id),
insurer_id UUID REFERENCES insurers(id),
quote_item_id UUID REFERENCES quote_items(id),
policy_number VARCHAR(128) UNIQUE,
product_id UUID REFERENCES products(id),
status VARCHAR(32) DEFAULT 'active',
start_date DATE,
end_date DATE,
premium_paid NUMERIC,
currency VARCHAR(8) DEFAULT 'THB',
issued_at TIMESTAMPTZ,
raw_payload JSONB,
created_at TIMESTAMPTZ DEFAULT now(),
updated_at TIMESTAMPTZ DEFAULT now()
);

-- 13. Policy Documents
CREATE TABLE policy_documents (
id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
policy_id UUID REFERENCES policies(id) ON DELETE CASCADE,
document_type VARCHAR(64),
url TEXT,
filename VARCHAR(255),
uploaded_at TIMESTAMPTZ DEFAULT now()
);

-- 14. Payments
CREATE TABLE payments (
id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
policy_id UUID REFERENCES policies(id),
user_id UUID REFERENCES users(id),
insurer_payment_reference VARCHAR(255),
amount NUMERIC,
currency VARCHAR(8) DEFAULT 'THB',
status VARCHAR(32),
gateway VARCHAR(64),
payload JSONB,
created_at TIMESTAMPTZ DEFAULT now()
);

-- 15. Commissions
CREATE TABLE commissions (
id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
policy_id UUID REFERENCES policies(id),
insurer_id UUID REFERENCES insurers(id),
amount NUMERIC,
status VARCHAR(32) DEFAULT 'expected',
paid_at TIMESTAMPTZ,
metadata JSONB,
created_at TIMESTAMPTZ DEFAULT now()
);

-- 16. Discounts
CREATE TABLE discounts (
id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
category_id INT REFERENCES insurance_categories(id),
name VARCHAR(255),
type VARCHAR(32),
value NUMERIC,
valid_from TIMESTAMPTZ,
valid_until TIMESTAMPTZ,
is_active BOOLEAN DEFAULT TRUE,
conditions JSONB
);

-- 17. CMS Pages
CREATE TABLE cms_pages (
id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
slug VARCHAR(255) UNIQUE,
title VARCHAR(255),
content TEXT,
locale VARCHAR(8) DEFAULT 'en',
is_active BOOLEAN DEFAULT TRUE,
updated_at TIMESTAMPTZ DEFAULT now()
);

-- 18. Banners
CREATE TABLE banners (
id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
title VARCHAR(255),
image_url TEXT,
link_url TEXT,
is_active BOOLEAN DEFAULT TRUE,
display_order INT DEFAULT 0
);

-- 19. Notifications
CREATE TABLE notifications (
id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
user_id UUID REFERENCES users(id),
channel VARCHAR(16),
template VARCHAR(128),
payload JSONB,
status VARCHAR(32) DEFAULT 'queued',
attempts INT DEFAULT 0,
scheduled_at TIMESTAMPTZ,
sent_at TIMESTAMPTZ
);

-- 20. Admin Users
CREATE TABLE admin_users (
id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
email VARCHAR(255) UNIQUE,
password_hash VARCHAR(255),
name VARCHAR(255),
is_super BOOLEAN DEFAULT FALSE,
twofa_enabled BOOLEAN DEFAULT FALSE,
created_at TIMESTAMPTZ DEFAULT now()
);

-- 21. Admin Logs
CREATE TABLE admin_logs (
id BIGSERIAL PRIMARY KEY,
admin_id UUID REFERENCES admin_users(id),
action VARCHAR(128),
resource_type VARCHAR(64),
resource_id VARCHAR(128),
ip_address INET,
user_agent TEXT,
payload JSONB,
created_at TIMESTAMPTZ DEFAULT now()
);

-- 22. Webhook Events
CREATE TABLE webhook_events (
id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
source VARCHAR(128),
event_type VARCHAR(128),
reference_id VARCHAR(255),
payload JSONB,
status VARCHAR(32) DEFAULT 'pending',
received_at TIMESTAMPTZ DEFAULT now(),
processed_at TIMESTAMPTZ
);

-- 23. KYC Documents
CREATE TABLE kyc_documents (
id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
user_id UUID REFERENCES users(id),
document_type VARCHAR(64),
url TEXT,
status VARCHAR(32) DEFAULT 'uploaded',
uploaded_at TIMESTAMPTZ DEFAULT now()
);

-- Index recommendations
CREATE INDEX idx_quotes_user_category ON quotes(user_id, category_id);
CREATE INDEX idx_quote_items_premium ON quote_items(premium);
CREATE INDEX idx_policies_user ON policies(user_id);
CREATE INDEX idx_policies_status ON policies(status);
CREATE INDEX idx_payments_status ON payments(status);
