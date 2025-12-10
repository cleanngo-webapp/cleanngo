# Supabase Storage Setup Guide

This guide explains how to configure Supabase Storage for your Laravel gallery images.

## Why Supabase Storage?

- ✅ **Free tier**: 1GB storage, 2GB bandwidth/month
- ✅ **Persistent**: Files never disappear
- ✅ **Fast CDN**: Global content delivery
- ✅ **S3-compatible**: Works with Laravel's S3 driver
- ✅ **Easy setup**: Simple configuration

## Step 1: Create Supabase Project

1. **Sign up/Login to Supabase**
   - Go to [supabase.com](https://supabase.com) and sign in
   - Click **"New Project"**

2. **Create a Storage Bucket**
   - In your project dashboard, go to **Storage** (left sidebar)
   - Click **"New bucket"**
   - Name it (e.g., `gallery-images`)
   - **Important**: Enable **"Public bucket"** (so images can be accessed via URL)
   - Click **"Create bucket"**

## Step 2: Get Storage Credentials

1. **Get Your Project URL**
   - In Supabase dashboard, go to **Settings** → **API**
   - Copy your **Project URL** (e.g., `https://xxxxx.supabase.co`)

2. **Create Storage API Key**
   - Still in **Settings** → **API**
   - Find **"service_role"** key (⚠️ Keep this secret!)
   - Copy the **service_role** key

3. **Get S3 Credentials** (for S3-compatible access)
   - Go to **Settings** → **Storage**
   - You'll see S3-compatible credentials:
     - **Access Key ID**
     - **Secret Access Key**
     - **Endpoint** (S3 endpoint URL)

## Step 3: Configure Laravel

### Install Required Package

```bash
composer require league/flysystem-aws-s3-v3
```

### Add Environment Variables

Add these to your `.env` file (or Render environment variables):

```env
# Supabase Storage Configuration
SUPABASE_STORAGE_KEY=your_access_key_id
SUPABASE_STORAGE_SECRET=your_secret_access_key
SUPABASE_STORAGE_REGION=us-east-1
SUPABASE_STORAGE_BUCKET=gallery-images
SUPABASE_STORAGE_ENDPOINT=https://xxxxx.supabase.co/storage/v1/s3
SUPABASE_STORAGE_URL=https://xxxxx.supabase.co/storage/v1/object/public
```

**Replace:**
- `your_access_key_id` - Your Supabase S3 Access Key ID
- `your_secret_access_key` - Your Supabase S3 Secret Access Key
- `gallery-images` - Your bucket name
- `xxxxx.supabase.co` - Your Supabase project URL

### Example .env Configuration

```env
SUPABASE_STORAGE_KEY=AKIAIOSFODNN7EXAMPLE
SUPABASE_STORAGE_SECRET=wJalrXUtnFEMI/K7MDENG/bPxRfiCYEXAMPLEKEY
SUPABASE_STORAGE_REGION=us-east-1
SUPABASE_STORAGE_BUCKET=gallery-images
SUPABASE_STORAGE_ENDPOINT=https://abcdefghijklmnop.supabase.co/storage/v1/s3
SUPABASE_STORAGE_URL=https://abcdefghijklmnop.supabase.co/storage/v1/object/public
```

## Step 4: Make Bucket Public

1. **In Supabase Dashboard** → **Storage** → Your bucket
2. Click **"Settings"** (gear icon)
3. Enable **"Public bucket"**
4. This allows images to be accessed via public URLs

## Step 5: Test the Setup

1. **Deploy your application** (or run locally)
2. **Upload a test image** through the admin gallery
3. **Check the database** - `image_path` should contain a full Supabase URL like:
   ```
   https://xxxxx.supabase.co/storage/v1/object/public/gallery-images/gallery/1234567890_abc123.jpg
   ```
4. **View the image** - It should display correctly in both admin and customer views

## How It Works

1. **Upload**: When you upload an image, it's stored in Supabase Storage
2. **URL Storage**: The full Supabase public URL is saved in the `image_path` column
3. **Display**: The `GalleryImage` model's `url` attribute automatically:
   - Returns the URL directly if it's a full URL (Supabase)
   - Generates `asset('storage/...')` if it's a local path

## Migration from Local Storage

If you have existing images in local storage:

1. **Option 1**: Re-upload them through the admin panel (they'll go to Supabase)
2. **Option 2**: Manually upload to Supabase and update database records with the new URLs

## Troubleshooting

**Problem**: Images not uploading
- **Solution**: Check that all environment variables are set correctly
- Verify bucket name matches `SUPABASE_STORAGE_BUCKET`
- Check that bucket is set to "Public"

**Problem**: Images upload but don't display
- **Solution**: Make sure bucket has "Public bucket" enabled
- Verify `SUPABASE_STORAGE_URL` is correct
- Check the URL format in the database

**Problem**: "Access Denied" errors
- **Solution**: Verify S3 credentials are correct
- Check bucket permissions
- Ensure service_role key has storage access

**Problem**: "Endpoint not found" errors
- **Solution**: Verify `SUPABASE_STORAGE_ENDPOINT` includes `/storage/v1/s3`
- Check your Supabase project URL is correct

## Free Tier Limits

- **Storage**: 1GB free
- **Bandwidth**: 2GB/month free
- **File size**: Up to 50MB per file (configurable)

For production, consider upgrading if you exceed these limits.

## Security Notes

⚠️ **Important**: 
- Never commit your `.env` file to git
- The `service_role` key has full access - keep it secret
- Use environment variables in production (Render, etc.)

## Next Steps

After setup:
1. ✅ Images are stored in Supabase Storage
2. ✅ Full URLs are saved in the database
3. ✅ Images persist across deployments
4. ✅ Fast CDN delivery worldwide

Enjoy your Supabase-powered gallery! 🎉

