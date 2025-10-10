#!/usr/bin/env bash
set -euo pipefail

echo "=== Starting multisite initialization (Windows-safe, PHP insert, non-fatal DB wait) ==="

# 0) Chờ wp-config.php do container wordpress tạo ra
until [ -f /var/www/html/wp-config.php ]; do
  echo "Waiting for wp-config.php..."
  sleep 2
done

cd /var/www/html

# Quyền & chuẩn hóa
chmod u+rwx /var/www/html || true
chmod u+rw wp-config.php || true
sed -i 's/\r$//' wp-config.php || true

# Tắt verify TLS cho mysqli để tránh lỗi self-signed cert khi WP-CLI kết nối MySQL
if ! grep -q "MYSQL_CLIENT_FLAGS" wp-config.php; then
  wp config set MYSQL_CLIENT_FLAGS "MYSQLI_CLIENT_SSL_DONT_VERIFY_SERVER_CERT" \
    --type=constant --allow-root --path=/var/www/html || true
  echo "✓ Set MYSQL_CLIENT_FLAGS=MYSQLI_CLIENT_SSL_DONT_VERIFY_SERVER_CERT"
fi


# In thông tin DB từ wp-config để debug (có thể lỗi nếu DB chưa sẵn sàng, nhưng không sao)
echo "--- wp-config DB constants (from file text) ---"
php -r '$c=file_get_contents("wp-config.php"); if(preg_match_all("/define\\(\\s*\\x27([^\\x27]+)\\x27\\s*,\\s*\\x27([^\\x27]*)\\x27\\s*\\)/",$c,$m)){foreach($m[1] as $i=>$k){ if(in_array($k,["DB_NAME","DB_USER","DB_PASSWORD","DB_HOST"])) echo $k."=".$m[2][$i]."\n";}}' || true
echo "-----------------------------------------------"

# 1) CHÈN MULTISITE DEFINES TRƯỚC (để /wp-admin/network/ hoạt động ngay)
if ! grep -q "DOMAIN_CURRENT_SITE" wp-config.php; then
  cat > /tmp/insert_multisite.php <<'PHP'
<?php
$f = '/var/www/html/wp-config.php';
$s = file_get_contents($f);
if (strpos($s, "DOMAIN_CURRENT_SITE") === false) {
    $defs = <<<'D'
define('WP_ALLOW_MULTISITE', true);
define('MULTISITE', true);
define('SUBDOMAIN_INSTALL', false);
define('DOMAIN_CURRENT_SITE', 'localhost:8002');
define('PATH_CURRENT_SITE', '/');
define('SITE_ID_CURRENT_SITE', 1);
define('BLOG_ID_CURRENT_SITE', 1);
D;

    $marker = "/* That's all, stop editing! Happy publishing. */";
    $pos = strpos($s, $marker);
    if ($pos === false) {
        $marker = "require_once ABSPATH . 'wp-settings.php';";
        $pos = strpos($s, $marker);
    }
    if ($pos !== false) {
        $s = substr($s, 0, $pos) . $defs . "\n" . substr($s, $pos);
    } else {
        $s .= "\n".$defs."\n";
    }
    file_put_contents($f, $s);
    echo "Inserted MULTISITE defines\n";
} else {
    echo "MULTISITE defines already exist\n";
}
PHP
  php /tmp/insert_multisite.php
  rm -f /tmp/insert_multisite.php
else
  echo "MULTISITE defines already exist (grep)"
fi

# 2) .htaccess chuẩn Multisite
chmod u+w /var/www/html || true
cat > .htaccess <<'HTACCESS'
RewriteEngine On
RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
RewriteBase /
RewriteRule ^index\.php$ - [L]
RewriteRule ^([_0-9a-zA-Z-]+)?/wp-admin$ $1wp-admin/ [R=301,L]
RewriteCond %{REQUEST_FILENAME} -f [OR]
RewriteCond %{REQUEST_FILENAME} -d
RewriteRule ^ - [L]
RewriteRule ^([_0-9a-zA-Z-]+/)?(wp-(content|admin|includes).*) $2 [L]
RewriteRule ^([_0-9a-zA-Z-]+/)?(.*\.php)$ $2 [L]
RewriteRule . index.php [L]
HTACCESS
chown 33:33 .htaccess || true
echo "✓ Wrote .htaccess"

# 3) ĐỢI DB (không exit sớm), cố gắng sửa hằng DB nếu cần
echo "Waiting for database connection via wp db check..."
ok=0
for i in $(seq 1 60); do
  if wp db check --allow-root >/dev/null 2>&1; then
    ok=1; echo "DB connection OK (try $i)"; break
  fi
  echo "  -> not ready yet (try $i), sleeping 2s..."
  sleep 2
done

if [ "$ok" -ne 1 ]; then
  echo "DB still not reachable. Trying to (re)write DB constants from env..."
  wp config set DB_NAME     "${WORDPRESS_DB_NAME:-wordpress}"     --type=constant --allow-root --path=/var/www/html || true
  wp config set DB_USER     "${WORDPRESS_DB_USER:-wp}"            --type=constant --allow-root --path=/var/www/html || true
  wp config set DB_PASSWORD "${WORDPRESS_DB_PASSWORD:-wp}"        --type=constant --allow-root --path=/var/www/html || true
  wp config set DB_HOST     "${WORDPRESS_DB_HOST:-db:3306}"       --type=constant --allow-root --path=/var/www/html || true

  for i in $(seq 1 60); do
    if wp db check --allow-root >/dev/null 2>&1; then
      ok=1; echo "DB connection OK after rewrite (try $i)"; break
    fi
    echo "  -> still not ready (try $i), sleeping 2s..."
    sleep 2
  done
fi

# 4) Kích hoạt multisite nếu DB đã OK; nếu chưa OK thì bỏ qua (defines đã có)
if [ "$ok" -eq 1 ]; then
  if [ "$(wp eval 'echo is_multisite() ? 1 : 0;' --allow-root)" != "1" ]; then
    wp core multisite-install --subdomains=0 \
      --url="http://localhost:8002" \
      --title="My Network" \
      --admin_user=admin \
      --admin_password=admin123 \
      --admin_email=admin@example.com \
      --skip-email --allow-root
    echo "✓ Multisite installed successfully"
  else
    echo "Multisite already active, skipping installation."
  fi
else
  echo "DB not ready yet; kept Multisite defines and .htaccess. You can run multisite-install later."
fi

# 5) Trả quyền về www-data
chown -R 33:33 /var/www/html || true

echo "✅ init_multisite completed"
