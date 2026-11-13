# BAO FASHION

Website bán quần áo xây dựng bằng Laravel và MySQL. Khách hàng đăng ký, mua hàng và xem đơn của mình; admin quản lý sản phẩm và theo dõi các đơn mua.

## Công nghệ

| Thành phần | Công nghệ |
|---|---|
| Framework / backend | Laravel 9, PHP 8 |
| Giao diện | Blade, HTML, CSS, JavaScript |
| Cơ sở dữ liệu | MySQL |
| Build frontend | Vite 4, npm |
| Công cụ | Composer, VS Code, MySQL Workbench |

## Cài đặt và chạy

1. Tạo database MySQL `shop_quan_ao`, rồi cấu hình thông tin kết nối trong `.env`. Cổng ví dụ dưới đây là `3307`; thay bằng cổng MySQL trên máy của bạn.

   ```dotenv
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3307
   DB_DATABASE=shop_quan_ao
   DB_USERNAME=root
   DB_PASSWORD=
   ```

2. Chạy tại thư mục dự án:

   ```bash
   composer install
   npm install
   npm run build
   php artisan key:generate
   php artisan migrate --seed
   php artisan serve
   ```

3. Mở URL Artisan cung cấp, thường là `http://127.0.0.1:8000`.

Tạo tài khoản admin bằng cách đăng ký tài khoản trước, sau đó chạy `php artisan user:make-admin email@example.com`. Khách đăng nhập tại `/login`; admin đăng nhập tại `/admin/login`.

## Bản phác thảo website

Wireframe trang chủ, luồng mua hàng và khu vực quản trị: [docs/wireframes/bao-fashion-wireframe.svg](docs/wireframes/bao-fashion-wireframe.svg).

# CHƯƠNG 2: THỰC NGHIỆM

## 2.1. Sơ đồ tuần tự

### 2.1.1. Đăng ký

```mermaid
%%{init: {"theme":"base","themeVariables":{"background":"#111111","primaryColor":"#111111","primaryTextColor":"#eeeeee","primaryBorderColor":"#eeeeee","lineColor":"#eeeeee","secondaryColor":"#111111","tertiaryColor":"#181818","actorBkg":"#111111","actorBorder":"#eeeeee","actorTextColor":"#eeeeee","actorLineColor":"#aaaaaa","signalColor":"#eeeeee","signalTextColor":"#eeeeee","labelBoxBkgColor":"#181818","labelBoxBorderColor":"#eeeeee","labelTextColor":"#eeeeee","loopTextColor":"#eeeeee","activationBkgColor":"#eeeeee","activationBorderColor":"#eeeeee","sequenceNumberColor":"#111111"}}}%%
sequenceDiagram
    actor KH as khách hàng
    participant UI as giao diện đăng ký (register.blade.php)
    participant R as xử lý đăng ký (routes/web.php)
    participant V as kiểm tra dữ liệu và băm mật khẩu
    participant U as User Model
    participant DB as cơ sở dữ liệu (MySQL - users)

    KH->>UI: 1. Mở trang đăng ký
    UI-->>KH: 2. Hiển thị form đăng ký
    KH->>UI: 3. Nhập họ tên, email và mật khẩu
    UI->>R: 4. POST /register
    activate R
    R->>V: 5. Kiểm tra dữ liệu và email duy nhất
    alt Đăng ký thất bại / dữ liệu không hợp lệ
        V-->>R: 6a. Trả lỗi xác thực
        R-->>UI: 7a. Trả lại form cùng thông báo lỗi
        UI-->>KH: 8a. Hiển thị lỗi để nhập lại
    else Đăng ký thành công
        V-->>R: 6b. Dữ liệu hợp lệ
        R->>V: 7b. Băm mật khẩu
        R->>U: 8b. Tạo tài khoản khách (is_admin = false)
        U->>DB: 9b. INSERT users
        DB-->>U: 10b. Trả kết quả lưu
        U-->>R: 11b. Trả tài khoản mới
        R->>R: 12b. Đăng nhập và tạo session
        R-->>UI: 13b. Trả kết quả thành công
        UI-->>KH: 14b. Chuyển về trang chủ
    end
    deactivate R
```

**Giải thích:** Hệ thống kiểm tra dữ liệu, băm mật khẩu trước khi lưu và tạo tài khoản khách hàng.

### 2.1.2. Đăng nhập

```mermaid
%%{init: {"theme":"base","themeVariables":{"background":"#111111","primaryColor":"#111111","primaryTextColor":"#eeeeee","primaryBorderColor":"#eeeeee","lineColor":"#eeeeee","secondaryColor":"#111111","tertiaryColor":"#181818","actorBkg":"#111111","actorBorder":"#eeeeee","actorTextColor":"#eeeeee","actorLineColor":"#aaaaaa","signalColor":"#eeeeee","signalTextColor":"#eeeeee","labelBoxBkgColor":"#181818","labelBoxBorderColor":"#eeeeee","labelTextColor":"#eeeeee","loopTextColor":"#eeeeee","activationBkgColor":"#eeeeee","activationBorderColor":"#eeeeee","sequenceNumberColor":"#111111"}}}%%
sequenceDiagram
    actor ND as khách hàng
    participant UI as giao diện đăng nhập (login.blade.php)
    participant C as xử lý đăng nhập (routes/web.php)
    participant DB as cơ sở dữ liệu (MySQL - users)
    participant M as kiểm tra quyền admin
    participant H as trang chủ / trang quản trị

    ND->>UI: 1. Mở trang đăng nhập
    UI-->>ND: 2. Hiển thị form đăng nhập
    ND->>UI: 3. Nhập email và mật khẩu
    UI->>C: 4. POST /login hoặc /admin/login
    activate C
    C->>DB: 5. Tìm tài khoản theo email
    DB-->>C: 6. Trả tài khoản và mật khẩu đã băm
    C->>C: 7. Auth::attempt, so sánh mật khẩu
    alt Đăng nhập thất bại / sai vai trò
        C-->>UI: 8a. Trả lỗi xác thực
        UI-->>ND: 9a. Hiển thị thông báo lỗi
    else Khách đăng nhập thành công
        C->>C: 10a. Tạo session
        C-->>UI: 11a. Trả kết quả thành công
        UI->>H: 12a. Chuyển đến trang chủ
        H-->>ND: 13a. Hiển thị trang chủ
    else Admin đăng nhập thành công
        C->>C: 10b. Tạo session
        C->>M: 11b. Kiểm tra is_admin khi vào /admin/*
        M-->>C: 12b. Cho phép truy cập
        C-->>UI: 13b. Trả kết quả thành công
        UI->>H: 14b. Chuyển đến trang quản trị
        H-->>ND: 15b. Hiển thị trang quản trị
    end
    deactivate C
```

**Giải thích:** Khách và admin dùng trang đăng nhập riêng. Laravel xác thực tài khoản và vai trò trước khi cho truy cập.

### 2.1.3. Đặt hàng

```mermaid
%%{init: {"theme":"base","themeVariables":{"background":"#111111","primaryColor":"#111111","primaryTextColor":"#eeeeee","primaryBorderColor":"#eeeeee","lineColor":"#eeeeee","secondaryColor":"#111111","tertiaryColor":"#181818","actorBkg":"#111111","actorBorder":"#eeeeee","actorTextColor":"#eeeeee","actorLineColor":"#aaaaaa","signalColor":"#eeeeee","signalTextColor":"#eeeeee","labelBoxBkgColor":"#181818","labelBoxBorderColor":"#eeeeee","labelTextColor":"#eeeeee","loopTextColor":"#eeeeee","activationBkgColor":"#eeeeee","activationBorderColor":"#eeeeee","sequenceNumberColor":"#111111"}}}%%
sequenceDiagram
    actor KH as khách hàng
    participant UI as giao diện giỏ hàng / thanh toán
    participant R as xử lý đơn hàng (routes/web.php)
    participant S as session cart
    participant P as cơ sở dữ liệu (MySQL - products)
    participant O as cơ sở dữ liệu (MySQL - orders)

    KH->>UI: 1. Nhập thông tin giao hàng, xác nhận
    UI->>R: 2. POST /checkout
    activate R
    R->>S: 3. Đọc giỏ hàng
    S-->>R: 4. Trả danh sách sản phẩm
    R->>P: 5. Kiểm tra sản phẩm và tồn kho
    P-->>R: 6. Trả số lượng hiện có
    alt Giỏ hàng rỗng / tồn kho không đủ
        R-->>UI: 7a. Trả thông báo lỗi
        UI-->>KH: 8a. Yêu cầu kiểm tra lại giỏ hàng
    else Sản phẩm còn đủ
        R->>P: 7b. Khóa dòng, kiểm tra tồn kho lần cuối
        R->>O: 8b. Tạo đơn hàng gắn với user_id
        loop Với từng sản phẩm
            R->>O: 9b. Lưu tên, số lượng và giá đã mua
            R->>P: 10b. Trừ số lượng tồn kho
        end
        O-->>R: 11b. Commit transaction
        R->>S: 12b. Xóa giỏ hàng
        R-->>UI: 13b. Chuyển tới chi tiết đơn
        UI-->>KH: 14b. Hiển thị đơn hàng thành công
    end
    deactivate R
```

**Giải thích:** Đơn hàng và chi tiết được lưu trong transaction; hệ thống kiểm tra và trừ tồn kho trước khi báo đặt hàng thành công.

### 2.1.4. Quản trị sản phẩm và theo dõi đơn

```mermaid
%%{init: {"theme":"base","themeVariables":{"background":"#111111","primaryColor":"#111111","primaryTextColor":"#eeeeee","primaryBorderColor":"#eeeeee","lineColor":"#eeeeee","secondaryColor":"#111111","tertiaryColor":"#181818","actorBkg":"#111111","actorBorder":"#eeeeee","actorTextColor":"#eeeeee","actorLineColor":"#aaaaaa","signalColor":"#eeeeee","signalTextColor":"#eeeeee","labelBoxBkgColor":"#181818","labelBoxBorderColor":"#eeeeee","labelTextColor":"#eeeeee","loopTextColor":"#eeeeee","activationBkgColor":"#eeeeee","activationBorderColor":"#eeeeee","sequenceNumberColor":"#111111"}}}%%
sequenceDiagram
    actor AD as admin
    participant UI as giao diện quản trị (resources/views/admin)
    participant R as xử lý quản trị (ProductController)
    participant M as middleware kiểm tra is_admin
    participant P as cơ sở dữ liệu (MySQL - products)
    participant O as cơ sở dữ liệu (MySQL - orders và users)

    AD->>UI: 1. Đăng nhập tại /admin/login
    UI->>R: 2. Gửi yêu cầu quản trị
    R->>M: 3. Kiểm tra quyền admin
    M-->>R: 4. Tài khoản hợp lệ
    alt Quản lý sản phẩm
        AD->>UI: 5a. Mở /admin/products
        UI->>R: 6a. Xem, thêm, sửa hoặc xóa sản phẩm
        activate R
        R->>P: 7a. Đọc hoặc cập nhật products
        P-->>R: 8a. Trả kết quả
        R-->>UI: 9a. Hiển thị danh sách / thông báo
        deactivate R
    else Theo dõi đơn hàng
        AD->>UI: 5b. Mở /admin/orders
        UI->>R: 6b. Yêu cầu danh sách đơn
        activate R
        R->>O: 7b. Đọc đơn, người mua và chi tiết
        O-->>R: 8b. Trả thông tin đơn hàng
        R-->>UI: 9b. Hiển thị người mua, sản phẩm, địa chỉ, trạng thái
        deactivate R
    end
    UI-->>AD: 10. Hiển thị kết quả
```

**Giải thích:** Middleware chỉ cho admin quản lý sản phẩm và xem đơn mua; khách không có quyền truy cập.

## 2.2. PERT và đường găng

Thời lượng dưới đây là **ước lượng minh họa** vì chưa có bảng công việc thực tế của Bài nhận thức 3. `te` là thời lượng kỳ vọng; công việc có dự trữ bằng 0 thuộc đường găng.

```mermaid
%%{init: {"theme":"base","themeVariables":{"background":"#111111","primaryColor":"#171815","primaryTextColor":"#f6f6f2","primaryBorderColor":"#f6f6f2","lineColor":"#f6f6f2","secondaryColor":"#24251f","tertiaryColor":"#24251f","fontFamily":"Arial"}}}%%
flowchart LR
    A["A · Khảo sát yêu cầu<br/>2 ngày"] --> B["B · Thiết kế CSDL<br/>2 ngày"]
    B --> C["C · Đăng ký, đăng nhập<br/>2 ngày"]
    B --> D["D · Danh mục, sản phẩm<br/>3 ngày"]
    C --> E["E · Giỏ hàng, đặt hàng<br/>3 ngày"]
    D --> E
    E --> F["F · Phân quyền, theo dõi đơn<br/>2 ngày"]
    F --> G["G · Tích hợp, kiểm thử<br/>2 ngày"]
    G --> H["H · Demo, báo cáo<br/>1 ngày"]

    classDef critical fill:#171815,stroke:#d7ff3f,color:#f6f6f2,stroke-width:3px
    classDef buffer fill:#24251f,stroke:#a6a79d,color:#f6f6f2,stroke-width:2px
    class A,B,D,E,F,G,H critical
    class C buffer
    linkStyle 0 stroke:#d7ff3f,stroke-width:4px
    linkStyle 2 stroke:#d7ff3f,stroke-width:4px
    linkStyle 4 stroke:#d7ff3f,stroke-width:4px
    linkStyle 5 stroke:#d7ff3f,stroke-width:4px
    linkStyle 6 stroke:#d7ff3f,stroke-width:4px
    linkStyle 7 stroke:#d7ff3f,stroke-width:4px
```

| Mã | Công việc | Trước | te (ngày) | Dự trữ (ngày) |
|---|---|---|---:|---:|
| A | Khảo sát yêu cầu | — | 2 | 0 |
| B | Thiết kế cơ sở dữ liệu | A | 2 | 0 |
| C | Đăng ký và đăng nhập | B | 2 | 1 |
| D | Danh mục và quản lý sản phẩm | B | 3 | 0 |
| E | Giỏ hàng và đặt hàng | C, D | 3 | 0 |
| F | Phân quyền admin, theo dõi đơn | E | 2 | 0 |
| G | Tích hợp và kiểm thử | F | 2 | 0 |
| H | Demo và báo cáo | G | 1 | 0 |

Đường găng: **A → B → D → E → F → G → H**, tổng cộng **15 ngày ước tính**. Nhánh C có một ngày dự trữ.

## 2.3. Demo chức năng

| Chức năng | Đường dẫn | Quy trình / kết quả |
|---|---|---|
| 2.3.1 Đăng ký | `/register` | Nhập thông tin → kiểm tra email/mật khẩu → lưu tài khoản khách. |
| 2.3.2 Đăng nhập | `/login`, `/admin/login` | Xác thực tài khoản → kiểm tra vai trò → chuyển đến trang phù hợp. |
| 2.3.3 Đặt hàng | `/products`, `/cart`, `/checkout` | Chọn hàng → kiểm tra tồn kho → nhập thông tin nhận hàng → tạo đơn. |
| 2.3.4 Quản trị | `/admin/products`, `/admin/orders` | Admin CRUD sản phẩm và theo dõi người mua, mặt hàng, địa chỉ, trạng thái. |

Khi thuyết trình, mở website bằng `php artisan serve` và chụp ảnh kết quả thật của từng luồng. Bộ kiểm thử gần nhất: **15 tests passed** (`php artisan test`).

## 2.4. Cơ sở dữ liệu

```mermaid
%%{init: {"theme":"base","themeVariables":{"background":"#111111","primaryColor":"#171815","primaryTextColor":"#f6f6f2","primaryBorderColor":"#f6f6f2","lineColor":"#f6f6f2","secondaryColor":"#24251f","tertiaryColor":"#24251f","fontFamily":"Arial","entityBkg":"#171815","entityBorder":"#d7ff3f","entityTextColor":"#f6f6f2","relationshipLabelBackground":"#111111"}}}%%
erDiagram
    USERS ||--o{ ORDERS : places
    CATEGORIES ||--o{ PRODUCTS : contains
    ORDERS ||--|{ ORDER_ITEMS : includes
    PRODUCTS o|--o{ ORDER_ITEMS : references

    USERS {
        bigint id PK
        string name
        string email UK
        string password
        boolean is_admin
    }
    CATEGORIES {
        bigint id PK
        string name
        text description
    }
    PRODUCTS {
        bigint id PK
        bigint category_id FK
        string name
        decimal price
        int quantity
        string image
    }
    ORDERS {
        bigint id PK
        bigint user_id FK
        decimal total
        string status
        string phone
        string address
    }
    ORDER_ITEMS {
        bigint id PK
        bigint order_id FK
        bigint product_id FK
        string product_name
        int quantity
        decimal price
    }
```

| Bảng | Nội dung |
|---|---|
| `users` | Tài khoản và vai trò (`is_admin`); mật khẩu lưu dạng băm. |
| `categories` | Danh mục sản phẩm. |
| `products` | Sản phẩm, danh mục, giá và tồn kho. |
| `orders` | Người đặt, tổng tiền, trạng thái và thông tin giao hàng. |
| `order_items` | Sản phẩm, số lượng, giá và tên lưu tại thời điểm đặt hàng. |

Quan hệ chính: `categories` 1–N `products`; `users` 1–N `orders`; `orders` 1–N `order_items`. Khi sản phẩm bị xóa, chi tiết đơn vẫn giữ tên và giá đã mua.

Trong MySQL Workbench, chọn schema `shop_quan_ao`, mở tab Query và chạy **từng truy vấn riêng** (chỉ sao chép SQL, không sao chép dấu ```):

**Tài khoản:**

```sql
SELECT id, name, email, is_admin, created_at
FROM users
ORDER BY created_at DESC;
```

**Đơn hàng và người đặt (giờ Việt Nam):**

```sql
SELECT o.id AS ma_don, u.name AS nguoi_dat, u.email,
       o.phone, o.address, o.total, o.status,
       DATE_ADD(o.created_at, INTERVAL 7 HOUR) AS gio_dat_viet_nam
FROM orders AS o
JOIN users AS u ON u.id = o.user_id
ORDER BY o.created_at DESC;
```

**Sản phẩm trong đơn:**

```sql
SELECT order_id, product_name, quantity, price
FROM order_items
ORDER BY order_id DESC, id;
```

Seeder tạo 4 danh mục và 6 sản phẩm mẫu. Database lưu thời gian UTC; website và truy vấn trên hiển thị giờ Việt Nam (UTC+7).

## Tài liệu trình bày

Mở [slide thuyết trình](docs/BAO_FASHION_THUYET_TRINH.html) bằng trình duyệt. Dùng phím mũi tên để chuyển slide hoặc chọn **In / lưu PDF**.
