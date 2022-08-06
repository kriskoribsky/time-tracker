<?php 



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

                    <label for="project-selection">Project:</label>
                    <select class="selection-dropdown" id="project-selection" disabled>
                        <option value="" selected></option>
                    </select>

                    <input type="hidden" name="project-id" value="">
                </div>

                <div class="form-row">

                    <label>
                        <span>Task name: </span>
                        <input name="task-name" type="text" required>
                    </label>

                </div>

                <div class="form-row button-margin">

                    <div class="alert warning hidden" role="alert">Please enter the correct format of values and try again.</div>

                    <div class="a-btn-and-btn">
                        <!-- <a class="form-btn btn-secondary btn-with-alert a-btn" href="javascript: window.history.back();">
                        <i class="fa-solid fa-arrow-left"></i><span>Go back</span>
                        </a> -->
                        <button type="submit" name="change-configuration" value="new-session-inputs" class="form-btn btn-submit btn-with-alert">
                            <i class="fa-solid fa-check"></i><span>Save changes</span>
                        </button>
                    </div>

                 </div>

            </form>

        </section>

    </div>

</div>

<?php require_once Helper\Path::build_path(PUBLIC_PATH, 'view', 'pages', 'inc', 'footer.php'); ?>