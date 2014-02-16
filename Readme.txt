1. Download Sourcode Elearning
2. Download bản Zend Framework 1 ( http://www.zend.com/en/company/community/downloads ) về. 
    Copy thư viện Zend trong ZendFramework-1.12.3\library vừa được giải nén vào thư mục library trong Sourcecode

3. Tạo cơ sở dữ liệu với tên " elearning " và import file " elearning.sql " trong thư mục data trong sourcecode
4. Sửa các thông sô username và password kết nối với cơ sở dữ liệu trong file application/configs/application.ini

==================Có thể nhét sourcecode vào thư muc htdocs rồi chạy, hoặc có thể cấu hình tiếp như sau:
5. Copy file " elearning.conf " trong thư mục data của sourcecode vào " \apache\conf\extra ".
Lưu ý cần sửa đường dẫn đến nơi đặt sourcecode trong file " elearning.conf " này.

6. Thêm dòng:

Include "conf/extra/elearning.conf"

trong file " httpd.conf " trong " \apache\conf "

7. thêm dòng
127.0.0.1		elearning.com

trong file hosts.

8. Ok, done! giờ có thể truy cập hệ thống theo đường link:

http://elearning.com
