

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


// Arama kutusunu açıp kapatma işlevi
function toggleSearchInput() {
    const searchBox = document.getElementById("search-box");
    searchBox.style.display = searchBox.style.display === "block" ? "none" : "block";
    if (searchBox.style.display === "block") {
        document.getElementById("searchInput").focus(); // Arama kutusuna odaklanma
    }
}

// Enter tuşuna basıldığında arama ve yönlendirme işlevi
document.getElementById("searchInput").addEventListener("keypress", function (event) {
    if (event.key === "Enter") {
        searchAndRedirect(event.target.value.toLowerCase());
    }
});

// Kategorilerin bulunduğu sayfanın URL'si
const categories = {
    "ana yemekler": {
        url: "ana_yemekler.html",
        dishes: [
            { name: "Ciğer Yahnisi", id: "ciger-yahnisi" },
            { name: "Baharatlı Piliç Külbastı", id: "baharatli-pilic-kulbasti" },
            { name: "Bonfile Kavurma", id: "bonfile-kavurma" },
            { name: "Köri Soslu Tavuk", id: "kori-soslu-tavuk" },
            { name: "Çökertme Kebabı", id: "cokertme-kebabi" },
            { name: "Beş Kardeş Yahnisi", id: "bes-kardes-yahnisi" },
            { name: "Kuzu Etli Enginar Yahni", id: "kuzu-etli-enginar-yahni" },
            { name: "Sakız Yahnisi", id: "sakiz-yahnisi" },
            { name: "Karnabahar Kavurması", id: "karnabahar-kavurmasi" },
            { name: "Kişniş Soslu Somon Balığı", id: "Kisnis-Soslu-Somon-Baligi" },
        ]
    },
    "sulu yemekler": {
        url: "sulu_yemekler.html",
        dishes: [
            { name: "Tarator", id: "tarator" },
            { name: "Mercimek Çorbası", id: "mercimek-corba" },
            { name: "Sebze Çorbası", id: "sebze-corba" }
        ]
    },
    "karbonhidrat lezzetleri": {
        url: "kuru_yemekler.html",
        dishes: [
            { name: "Pilav Üstü Döner", id: "pilav-ustu-doner" },
            { name: "Simit Kebabı", id: "simit-kebabi" },
            { name: "Sultan Kebabı", id: "sultan-kebabi" }
        ]
    },
    "aperatifler": {
        url: "aperatifler.html",
        dishes: [
            { name: "Zeytinyağlı Enginar", id: "zeytinyagli-enginar" },
            { name: "Humus", id: "humus" },
            { name: "Bruschetta", id: "bruschetta" }
        ]
    },
    "tatlilar": {
        url: "tatlilar.html",
        dishes: [
            { name: "Cheesecake", id: "cheesecake" },
            { name: "Baklava", id: "baklava" },
            { name: "Tiramisu", id: "tiramisu" }
        ]
    }
};

// Arama fonksiyonu
function searchAndRedirect(query) {
    // Kategori ve yemekleri kontrol et
    for (const category in categories) {
        if (category.includes(query)) {
            window.location.href = categories[category].url; // Kategori sayfasına yönlendir
            return;
        }
        const foundDish = categories[category].dishes.find(dish => dish.name.toLowerCase() === query);
        if (foundDish) {
            window.location.href = `${categories[category].url}#${foundDish.id}`; // Yemek ID'si ile yönlendirme
            return;
        }
    }
    alert("Aradığınız yemek veya kategori bulunamadı.");
}



        