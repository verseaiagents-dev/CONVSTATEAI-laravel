React Production Kuralları
* React projesinde kullanılan tüm kütüphaneler tüm tarayıcılarda çalışabilecek şekilde gömülecek. Proje içinde kullanılan css verileri style-loader ile min.js içine gömülecek. Proje içinde kullanılan widgetlar içine gömülecekenbaşta react ve reatdom içine gömülecek sıraya göre işlem yapılacak.
* fetch edilen tüm api verileri laravel sunucu https://convstateai.com ile birlikte çalışacak şekilde düzenleme yapılmalı. diğer türlü çalışmaz biz yaptığımız uygulamanın çalışmasını istiyoruz.

Geresiz loglar, ve hata mesajları console.log ve error tarafından görülmesini engelle

Build etme işlemi bittikten sonra laravel projesi public/embed içine convstateai.min.js olarak kopyala