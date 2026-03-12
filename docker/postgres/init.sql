-- ===================================
-- AUKCIJSKA PLATFORMA - POSTGRES INIT
-- ===================================
-- Initial database setup script

-- Create extensions
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";
CREATE EXTENSION IF NOT EXISTS "pg_trgm"; -- For fuzzy search
CREATE EXTENSION IF NOT EXISTS "btree_gin"; -- For composite indexes

-- Note: Laravel migrations will create the actual tables
-- This script is for database-level extensions and initial setup

-- Set timezone
SET timezone TO 'Europe/Sarajevo';

-- Create function for updated_at trigger
CREATE OR REPLACE FUNCTION update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = NOW();
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- Create function for generating short UUIDs
CREATE OR REPLACE FUNCTION generate_short_uuid()
RETURNS TEXT AS $$
BEGIN
    RETURN substring(md5(random()::text) from 1 for 12);
END;
$$ LANGUAGE plpgsql;

-- Grant permissions
GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA public TO aukcije;
GRANT ALL PRIVILEGES ON ALL SEQUENCES IN SCHEMA public TO aukcije;
