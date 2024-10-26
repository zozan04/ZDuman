document.getElementById("light").addEventListener("click", function() {
    const body = document.body;
    const themeIcon = document.getElementById("themeIcon");
    const circle = document.querySelector("light .circle"); // .light .circle olarak güncellendi

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
