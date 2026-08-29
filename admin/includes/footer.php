    </div> <!-- End .main-content -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggle = document.querySelector('.admin-menu-toggle');
        const menu = document.querySelector('.sidebar-menu');
        if (toggle && menu) {
            toggle.addEventListener('click', function() {
                menu.classList.toggle('active');
                toggle.innerHTML = menu.classList.contains('active') ? '&#10006;' : '&#9776;';
            });
        }
    });
    </script>
</body>
</html>
