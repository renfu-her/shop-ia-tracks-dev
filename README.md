# 購物平台後台管理系統

基於 Laravel 12 和 Filament 3 開發的購物平台後台管理系統，提供完整的商品管理、會員管理、輪播管理等功能。

## 🚀 技術棧

- **後端框架**: Laravel 12.x
- **PHP 版本**: ^8.2
- **管理後台**: Filament 3.3
- **資料庫**: SQLite (開發環境)
- **圖片處理**: Intervention Image 3.11
- **富文本編輯器**: TinyEditor 2.4
- **日期選擇器**: Flatpickr 3.2

## 📋 功能特色

### 🛍️ 商品管理
- **商品分類**: 支援無限層級分類結構
- **商品管理**: 完整的商品資訊管理，包含價格、庫存、規格等
- **商品圖片**: 支援多張圖片上傳，可設定主要圖片
- **商品標籤**: 支援商品標籤管理
- **特色商品**: 可標記特色商品
- **發布控制**: 支援商品發布時間控制

### 👥 使用者管理
- **管理者**: 支援 admin/manager 角色權限
- **會員管理**: 完整的會員資料管理
- **個人資料**: 包含姓名、電話、性別、生日、地址等

### 🎯 輪播管理
- **多類型輪播**: 支援首頁、商品、關於我們三種類型
- **時間控制**: 支援開始和結束時間設定
- **商品關聯**: 可關聯特定商品
- **排序功能**: 支援自定義排序

### ℹ️ 關於我們
- **內容管理**: 使用富文本編輯器
- **聯絡資訊**: 支援多種聯絡方式
- **社群媒體**: 支援社群媒體連結

## 🛠️ 安裝步驟

### 1. 環境要求
- PHP ^8.2
- Composer
- Node.js & NPM (可選，用於前端資源編譯)

### 2. 克隆專案
```bash
git clone <repository-url>
cd shop-ai-tracks
```

### 3. 安裝依賴
```bash
composer install
```

### 4. 環境設定
```bash
cp .env.example .env
php artisan key:generate
```

### 5. 資料庫設定
編輯 `.env` 檔案，設定資料庫連線：
```env
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database.sqlite
```

### 6. 執行資料庫遷移
```bash
php artisan migrate
```

### 7. 建立管理者帳號
```bash
php artisan make:filament-user
```

### 8. 啟動開發伺服器
```bash
php artisan serve
```

## 📁 專案結構

```
shop-ai-tracks/
├── app/
│   ├── Filament/Resources/          # Filament 資源
│   │   ├── UserResource.php        # 管理者管理
│   │   ├── MemberResource.php      # 會員管理
│   │   ├── ProductCategoryResource.php  # 商品分類
│   │   ├── ProductResource.php     # 商品管理
│   │   ├── BannerResource.php      # 輪播管理
│   │   └── AboutUsResource.php     # 關於我們
│   ├── Models/                     # Eloquent 模型
│   │   ├── User.php               # 管理者模型
│   │   ├── Member.php             # 會員模型
│   │   ├── ProductCategory.php    # 商品分類模型
│   │   ├── Product.php            # 商品模型
│   │   ├── ProductImage.php       # 商品圖片模型
│   │   ├── Banner.php             # 輪播模型
│   │   └── AboutUs.php            # 關於我們模型
│   └── Providers/                  # 服務提供者
├── database/
│   └── migrations/                 # 資料庫遷移檔案
├── public/                         # 公開資源
├── resources/                      # 前端資源
└── .cursor/rules/                  # Cursor 開發規範
    ├── filament.mdc               # Filament 開發規範
    ├── laravel-12.mdc             # Laravel 12 開發規範
    └── project.mdc                # 專案特定規範
```

## 🗄️ 資料庫結構

### 主要資料表

#### users (管理者)
- `id` - 主鍵
- `name` - 姓名
- `email` - 電子郵件 (唯一)
- `password` - 密碼
- `role` - 角色 (admin/manager)
- `is_active` - 啟用狀態
- `email_verified_at` - 郵件驗證時間
- `remember_token` - 記住我 Token
- `created_at`, `updated_at` - 時間戳記
- `deleted_at` - 軟刪除時間

#### members (會員)
- `id` - 主鍵
- `name` - 姓名
- `email` - 電子郵件 (唯一)
- `phone` - 電話
- `password` - 密碼
- `address` - 地址資訊 (JSON)
- `birthday` - 生日
- `gender` - 性別 (male/female/other)
- `is_active` - 啟用狀態
- `email_verified_at` - 郵件驗證時間
- `remember_token` - 記住我 Token
- `created_at`, `updated_at` - 時間戳記
- `deleted_at` - 軟刪除時間

#### product_categories (商品分類)
- `id` - 主鍵
- `name` - 分類名稱
- `description` - 分類描述
- `image` - 分類圖片
- `parent_id` - 上層分類 ID (外鍵)
- `sort_order` - 排序
- `is_active` - 啟用狀態
- `created_at`, `updated_at` - 時間戳記
- `deleted_at` - 軟刪除時間

#### products (商品)
- `id` - 主鍵
- `name` - 商品名稱
- `description` - 商品描述
- `short_description` - 簡短描述
- `price` - 原價
- `sale_price` - 特價
- `stock` - 庫存
- `sku` - SKU (唯一)
- `barcode` - 條碼
- `category_id` - 分類 ID (外鍵)
- `specifications` - 商品規格 (JSON)
- `tags` - 商品標籤 (JSON)
- `is_featured` - 特色商品
- `is_active` - 啟用狀態
- `published_at` - 發布時間
- `created_at`, `updated_at` - 時間戳記
- `deleted_at` - 軟刪除時間

#### product_images (商品圖片)
- `id` - 主鍵
- `product_id` - 商品 ID (外鍵)
- `image_path` - 圖片路徑
- `alt_text` - 替代文字
- `sort_order` - 排序
- `is_primary` - 主要圖片
- `created_at`, `updated_at` - 時間戳記

#### banners (輪播)
- `id` - 主鍵
- `title` - 標題
- `description` - 描述
- `image` - 圖片
- `link` - 連結網址
- `type` - 類型 (product/about/homepage)
- `product_id` - 商品 ID (外鍵，可選)
- `sort_order` - 排序
- `is_active` - 啟用狀態
- `start_at` - 開始時間
- `end_at` - 結束時間
- `created_at`, `updated_at` - 時間戳記
- `deleted_at` - 軟刪除時間

#### about_us (關於我們)
- `id` - 主鍵
- `title` - 標題
- `content` - 內容
- `image` - 圖片
- `contact_info` - 聯絡資訊 (JSON)
- `social_media` - 社群媒體 (JSON)
- `is_active` - 啟用狀態
- `created_at`, `updated_at` - 時間戳記
- `deleted_at` - 軟刪除時間

## 🔧 開發規範

### Filament 開發規範
- 使用 Filament 3.3 版本
- 遵循 Laravel 12 最佳實踐
- 使用 TinyEditor 作為富文本編輯器
- 使用 Flatpickr 作為日期選擇器
- 圖片上傳使用 FileUpload 元件
- 所有 Create/Edit 頁面重定向到列表頁面

### Laravel 12 開發規範
- 使用 PHP 8.2+ 語法
- 實作服務層分離業務邏輯
- 使用表單請求驗證
- 實作軟刪除功能
- 使用事件驅動架構
- 編寫完整的測試

### 專案特定規範
- 商品分類支援無限層級結構
- 商品支援多圖片管理
- 輪播支援時間範圍控制
- 所有主要模型實作軟刪除
- 使用 JSON 欄位儲存複雜資料

## 🎨 管理後台功能

### 導航結構
```
網站管理
├── 管理者管理 (UserResource)
├── 會員管理 (MemberResource)
├── 輪播管理 (BannerResource)
└── 關於我們 (AboutUsResource)

商品管理
├── 商品分類 (ProductCategoryResource)
└── 商品管理 (ProductResource)
```

### 主要功能
1. **CRUD 操作**: 所有資源都支援完整的增刪改查
2. **搜尋功能**: 支援文字搜尋和篩選
3. **排序功能**: 支援自定義排序
4. **狀態管理**: 統一的啟用/停用狀態控制
5. **圖片管理**: 支援圖片上傳、編輯、刪除
6. **關聯管理**: 支援模型間的關聯關係
7. **時間控制**: 支援時間範圍設定

## 🚀 部署

### 生產環境設定
1. 設定適當的資料庫連線
2. 設定檔案儲存驅動
3. 設定快取驅動
4. 設定隊列驅動
5. 設定日誌驅動

### 環境變數
```env
APP_NAME=購物平台
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=shop_platform
DB_USERNAME=your_username
DB_PASSWORD=your_password

CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
SESSION_LIFETIME=120

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

## 📝 開發筆記

### 常用 Artisan 指令
```bash
# 建立 Filament Resource
php artisan make:filament-resource ResourceName --generate

# 建立模型
php artisan make:model ModelName

# 建立遷移
php artisan make:migration create_table_name

# 執行遷移
php artisan migrate

# 清除快取
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### 開發注意事項
1. 所有表單驗證使用 FormRequest 類別
2. 圖片上傳使用 Intervention Image 處理
3. 富文本內容使用 TinyEditor
4. 日期時間使用 Flatpickr
5. 所有主要模型實作軟刪除
6. 使用適當的索引優化查詢效能

## 🤝 貢獻

1. Fork 專案
2. 建立功能分支 (`git checkout -b feature/AmazingFeature`)
3. 提交變更 (`git commit -m 'Add some AmazingFeature'`)
4. 推送到分支 (`git push origin feature/AmazingFeature`)
5. 開啟 Pull Request

## 📄 授權

此專案採用 MIT 授權條款 - 詳見 [LICENSE](LICENSE) 檔案

## 📞 聯絡資訊

如有任何問題或建議，請透過以下方式聯絡：
- 專案 Issues: [GitHub Issues](https://github.com/your-username/shop-ai-tracks/issues)
- 電子郵件: your-email@example.com

---

**開發者**: [您的姓名]
**版本**: 1.0.0
**最後更新**: 2025年7月21日
