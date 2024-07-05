<!-- JS here -->
<script src="theme/main/assets/js/vendor/jquery-3.5.1.min.js"></script>
<script>
    $(document).ready(function(){
        function scrollToElemenent(id) {
            $('html, body').animate({
                scrollTop: $(id).offset().top - 150
            }, 0);
        }

        $(document).on("click", "*[data-scroll-to]", function() {
            $("#sidebar__close-btn").click();
            scrollToElemenent($(this).attr("data-scroll-to"));
        });

        $(window).on("load", function(){
            if (window.location.hash) {
                if ($(window.location.hash).length > 0) {
                    scrollToElemenent(window.location.hash);
                }
            }
        })
    })
</script>
<script src="theme/main/assets/js/vendor/waypoints.min.js"></script>
<script src="theme/main/assets/js/bootstrap.bundle.min.js"></script>
<script src="theme/main/assets/js/jquery.meanmenu.js"></script>
<script src="theme/main/assets/js/swiper-bundle.min.js"></script>
<script src="theme/main/assets/js/owl.carousel.min.js"></script>
<script src="theme/main/assets/js/jquery.fancybox.min.js"></script>
<script src="theme/main/assets/js/isotope.pkgd.min.js"></script>
<script src="theme/main/assets/js/parallax.min.js"></script>
<!-- <script src="theme/main/assets/js/backToTop.js"></script> -->
<script src="theme/main/assets/js/jquery.counterup.min.js"></script>
<script src="theme/main/assets/js/ajax-form.js"></script>
<script src="theme/main/assets/js/wow.min.js"></script>
<script src="theme/main/assets/js/imagesloaded.pkgd.min.js"></script>
<script src="theme/main/assets/js/main.js"></script>
<script src="https://kit.fontawesome.com/2ba10e709c.js" crossorigin="anonymous"></script>