        </main> 

        <footer>

            <span>Copyright © Time-tracker 2022</span>

        </footer>

    </div>

    <!-- set colors for the current SESSION's selected project group -->
    <script>
        document.body.style.setProperty('--primary-color', "<?php echo $_SESSION['primary_color']; ?>");
        document.body.style.setProperty('--primary-gradient-secondary-color', "<?php echo $_SESSION['secondary_gradient_color']; ?>");
        document.body.style.setProperty('--text-light', "<?php echo $_SESSION['text_color']; ?>");
    </script>

    <!-- Google Analytics: change UA-XXXXX-Y to be your site's ID. -->
    <script>
        window.ga = function () { ga.q.push(arguments) }; ga.q = []; ga.l = +new Date;
        ga('create', 'UA-XXXXX-Y', 'auto'); ga('set', 'anonymizeIp', true); ga('set', 'transport', 'beacon'); ga('send', 'pageview')
    </script>
    <script src="https://www.google-analytics.com/analytics.js" async></script>

</body>
</html>