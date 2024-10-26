

 // Tema değiştirme butonuna tıklama olayını dinleyin
document.getElementById("light").addEventListener("click", function() {
    const body = document.body;
    const themeIcon = document.getElementById("themeIcon");
    const circle = document.querySelector(".light.circle");

    if (body.classList.contains("light-mode")) {
        // Karanlık moda geçiş
        body.classList.remove("light-mode");
        body.classList.add("dark-mode");
        themeIcon.classList.remove("fa-sun");
        themeIcon.classList.add("fa-moon");

        // Çemberin içini siyah yap, ay ikonunu beyaz yap
        circle.style.backgroundColor = "black";
        themeIcon.style.color = "white";
    } else {
        // Aydınlık moda geçiş
        body.classList.remove("dark-mode");
        body.classList.add("light-mode");
        themeIcon.classList.remove("fa-moon");
        themeIcon.classList.add("fa-sun");

        // Çemberin içini beyaz yap, güneş ikonunu siyah yap
        circle.style.backgroundColor = "white";
        themeIcon.style.color = "black";
    }
});
   // Ana resim efelti için
document.addEventListener("DOMContentLoaded", function () {
    const mainText = document.querySelector(".main_text");

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    mainText.classList.add("animate"); // Göründüğünde animasyonu başlat
                } else {
                    mainText.classList.remove("animate"); // Görünümden çıkınca animasyonu sıfırla
                }
            });
        },
        { threshold: 0.5 } // Elemanın %50’si görünürse tetikler
    );

    observer.observe(mainText);
});
  //HHakkımızda efekti için
document.addEventListener("DOMContentLoaded", function () {
    const aboutContainer = document.querySelector(".about-container");
    const aboutDescription = document.querySelector(".about-description");

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    aboutContainer.classList.add("visible"); // Göründüğünde animasyonu başlat
                    aboutDescription.classList.add("animate"); // Metin animasyonu başlasın
                } else {
                    aboutContainer.classList.remove("visible"); // Görünümden çıkınca animasyonu sıfırla
                    aboutDescription.classList.remove("animate"); // Metin animasyonu sıfırlansın
                }
            });
        },
        { threshold: 0.5 } // Elemanın %50’si göründüğünde tetiklenir
    );

    observer.observe(aboutContainer); // "Hakkımızda" bölümünü gözlemleyelim
});

   //Yemek Kategorileri için
document.addEventListener("DOMContentLoaded", function() {
    const categories = document.querySelectorAll('.category');

    function checkVisibility() {
        const triggerBottom = window.innerHeight * 0.8; // Ekranın %80'inde tetikleme
        categories.forEach(category => {
            const box = category.getBoundingClientRect();
            if (box.top < triggerBottom) {
                category.classList.add('visible'); // Kategori görünür hale gelir
            } else {
                category.classList.remove('visible'); // Kategori görünmez olursa sınıfı kaldır
            }
        });
    }

    // Sayfa yüklendiğinde ve kaydırıldığında görünürlüğü kontrol et
    checkVisibility();
    window.addEventListener('scroll', checkVisibility);
});


        