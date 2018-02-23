# xe-php-built-in-router
XpressEngine PHP Built-in Web Server Router

## 무엇인가요?
XpressEngine 1.x 개발자 중 PHP Built-in WebServer 사용자를 위한 router 입니다.

그 동안 URL Rewrite 기능을 사용하지 못한 분들을 위해서 추가한 router 입니다. 
(NginX rewrite rule 을 참고하였습니다.)


## 어떻게 쓰나요?
해당 route.php 파일을 xe 폴더 아래에 복사하시고 PHP Built-in WebServer 실행 옵션에 추가합니다.

``php.exe -S 0.0.0.0:8000 -t ./ ./route.php``

(**주의** PHP include_path 가 XE 폴더 root 로 지정되어야 동작합니다.)