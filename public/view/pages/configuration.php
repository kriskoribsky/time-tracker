<?php 
// query DB for current configuration settings
$config_query = Sanitize::sanitize_html_query(Database::query('SELECT option_name, value FROM app_config'));

foreach ($config_query as $query) {
    $config[$query['option_name']] = $query['value'];
}

?>


<?php require_once Helper\Path::build_path(PUBLIC_PATH, 'view', 'pages', 'inc', 'header.php'); ?>

<div class="main-content">

    <h1>
        <span>Configuration</span>
    </h1>

    <div class="content-statistics-wrapper ">

        <section class="data-section shadow configuration-options">

            <h2>Configure default app functionality</h2>

            <form class="form" action="/forms" method="POST">


                <div class="form-row">

                    <label>
                        <span>Salary: </span>
                        <input class="int-input" name="salary" type="number" min="1" max="100" step="0.5" required
                        value="<?php echo $config['salary_rate']; ?>">
                        <span>€/hour</span>
                    </label>

                </div>

                <div class="form-row">

                    <label class="switch-label" title="All time-related information will be displayed as net coding-time.">
                        <span>Net time format: </span>

                        <span class="switch">
                            <input class="switch-checkbox" name="show-net-format" type="checkbox" 
                            <?php echo $config['display_net_time'] === '1' ? 'checked' : null; ?>>
                            <span class="slider"></span>
                        </span>

                    </label>

                </div>

                <div class="form-row button-margin">

                    <div class="alert warning hidden" role="alert">Please enter the correct format of values and try again.</div>

                    <div class="a-btn-and-btn">
                        <button type="submit" name="submit" value="configuration-change" class="form-btn btn-submit btn-with-alert">
                            <i class="fa-solid fa-check"></i><span>Save changes</span>
                        </button>
                    </div>

                 </div>

            </form>

        </section>

    </div>

</div>

<?php require_once Helper\Path::build_path(PUBLIC_PATH, 'view', 'pages', 'inc', 'footer.php'); ?>