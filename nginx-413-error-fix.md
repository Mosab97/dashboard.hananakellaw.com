# Fixing Nginx "413 Request Entity Too Large" Error

## Problem Description
The nginx server was returning a **413 Request Entity Too Large** error when clients attempted to upload files or send requests larger than the default limit.

## Root Cause
The error occurs because nginx has a default maximum request body size limit of **1MB** (`client_max_body_size`). When requests exceed this limit, nginx returns the 413 error.

## Solution Applied

### Step 1: Backup Configuration
```bash
cp /etc/nginx/nginx.conf /etc/nginx/nginx.conf.backup
```

### Step 2: Locate Configuration File
```bash
find /etc -name "nginx.conf" 2>/dev/null
# Found: /etc/nginx/nginx.conf
```

### Step 3: Check Current Settings
```bash
grep -r "client_max_body_size" /etc/nginx/
# Result: No client_max_body_size directive found (using default 1MB)
```

### Step 4: Add client_max_body_size Directive
Added the following configuration to the `http` block in `/etc/nginx/nginx.conf`:

```nginx
http {
    # Increase maximum request body size to 100MB
    client_max_body_size 100M;
    
    # ... rest of configuration
}
```

### Step 5: Test Configuration
```bash
nginx -t
# nginx: the configuration file /etc/nginx/nginx.conf syntax is ok
# nginx: configuration file /etc/nginx/nginx.conf test is successful
```

### Step 6: Apply Changes
```bash
systemctl reload nginx
systemctl status nginx
```

## Configuration Details

### Before Fix
- Default `client_max_body_size`: **1MB**
- Result: 413 errors for requests > 1MB

### After Fix
- New `client_max_body_size`: **100MB**
- Result: Accepts requests up to 100MB

## Alternative Size Options

You can adjust the value based on your needs:

```nginx
# Different size options
client_max_body_size 10M;     # 10 megabytes
client_max_body_size 500M;    # 500 megabytes  
client_max_body_size 1G;      # 1 gigabyte
client_max_body_size 0;       # Disable limit (not recommended)
```

## Security Considerations

- **Don't disable the limit entirely** (`client_max_body_size 0;`) as it can lead to DoS attacks
- **Set appropriate limits** based on your application's actual needs
- **Monitor disk space** as larger uploads consume more storage
- **Consider timeout settings** for large file uploads:
  ```nginx
  client_body_timeout 60s;
  client_header_timeout 60s;
  ```

## Verification

To verify the fix is working:

1. **Check nginx status**: `systemctl status nginx`
2. **Test with a large file upload** through your application
3. **Monitor nginx error logs**: `tail -f /var/log/nginx/error.log`

## Files Modified

- **Main config**: `/etc/nginx/nginx.conf`
- **Backup created**: `/etc/nginx/nginx.conf.backup`

## Applied On
- **Date**: September 15, 2025
- **Server**: Ubuntu Linux
- **Nginx version**: Checked with `nginx -v`

## Additional Notes

- This setting applies **globally** to all server blocks unless overridden
- You can also set `client_max_body_size` per server block or location for more granular control
- Remember to reload nginx after any configuration changes: `systemctl reload nginx`

---

## Quick Reference Commands

```bash
# Check nginx syntax
nginx -t

# Reload nginx
systemctl reload nginx

# Check nginx status  
systemctl status nginx

# View nginx error logs
tail -f /var/log/nginx/error.log

# Find client_max_body_size settings
grep -r "client_max_body_size" /etc/nginx/
```
