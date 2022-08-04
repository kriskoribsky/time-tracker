<?php require_once Helper\Path::build_path(PUBLIC_PATH, 'view', 'pages', 'inc', 'header.php'); ?>

<div class="main-content">

    <h1>
        <span>Your projects</span>
        <a class="heading-icon" data-toggle="modal" data-target="#create-project-modal" href="#" title="Create new project"><i class="fa-solid fa-square-plus"></i></a>
    </h1>

    <div class="content-statistics-wrapper">

        <div class="content-statistics-wrapper-projects">

            <section class="data-section project-section shadow width-100">

                <h2>Elcop</h2>

                <table class="project-info">

                    <tr>
                        <th>Date created:</th>
                        <td>30.7.2022</td>
                    </tr>

                    <tr>
                        <th>Number of tasks:</th>
                        <td>5</td>
                    </tr>

                    <tr>
                        <th>Unpaid work-time:</th>
                        <td class="text-green">5 hours 1 minute</td>
                    </tr>

                    <tr>
                        <th>Total project work-time:</th>
                        <td>104 hours 54 minutes</td>
                    </tr>

                </table>

                <div class="controls hidden"></div>

            </section>

            <section class="data-section project-section shadow width-100">

                <h2>Jobin</h2>

                <table class="project-info">

                    <tr>
                        <th>Date created:</th>
                        <td>20.6.2021</td>
                    </tr>

                    <tr>
                        <th>Number of tasks:</th>
                        <td>985</td>
                    </tr>

                    <tr>
                        <th>Unpaid work-time:</th>
                        <td class="text-green">1 hour 0 minutes</td>
                    </tr>

                    <tr>
                        <th>Total project work-time:</th>
                        <td>1586 hours 20 minutes</td>
                    </tr>

                </table>

                <div class="controls hidden"></div>

            </section>

            <section class="data-section project-section shadow width-100">

                <h2>Elcop - prihlaska</h2>

                <table class="project-info">

                    <tr>
                        <th>Date created:</th>
                        <td>30.7.2022</td>
                    </tr>

                    <tr>
                        <th>Number of tasks:</th>
                        <td>0</td>
                    </tr>

                    <tr>
                        <th>Total project work-time:</th>
                        <td>1586 hours 20 minutes</td>
                    </tr>

                </table>

                <div class="controls hidden"></div>

            </section>

            <section class="data-section project-section shadow width-100">

                <h2>MiniRelaxMier</h2>

                <table class="project-info">

                    <tr>
                        <th>Date created:</th>
                        <td>1.8.2022</td>
                    </tr>

                    <tr>
                        <th>Number of tasks:</th>
                        <td>15</td>
                    </tr>

                    <tr>
                        <th>Unpaid work-time:</th>
                        <td class="text-green">89 hours 59 minutes</td>
                    </tr>

                    <tr>
                        <th>Total project work-time:</th>
                        <td>3 hours 20 minutes</td>
                    </tr>

                </table>

                <div class="controls hidden"></div>

            </section>

            <section class="data-section project-section shadow width-100">

                <h2>Time-tracker</h2>

                <table class="project-info">

                    <tr>
                        <th>Date created:</th>
                        <td>28.7.2022</td>
                    </tr>

                    <tr>
                        <th>Number of tasks:</th>
                        <td>5</td>
                    </tr>

                    <tr>
                        <th>Total project work-time:</th>
                        <td>1 hours 20 minutes</td>
                    </tr>

                </table>

                <div class="controls hidden"></div>

            </section>

        </div>

        <div class="content-statistics-wrapper-statistics">

            <section class="data-section overall-statistics shadow">

                <h2>Overall statistics</h2>

                <canvas></canvas>

                <table class="text-left">

                    <tr>
                        <th>Working time past 7 days:</th>
                        <td>56 hours 41 minutes</td>
                    </tr>

                    <tr>
                        <th>Total unpaid work-time:</th>
                        <td class="text-green">56 hours 41 minutes</td>
                    </tr>

                    <tr>
                        <th>Hourly wage:</th>
                        <th>6 €</th>
                    </tr>

                    <tr>
                        <th>Salary:</th>
                        <td class="text-green"><strong>339,99 €</strong></td>
                    </tr>

                </table>

                <div class="overall-statistics-foot">

                    <hr>

                    <!-- <button type="submit" is inside pop-up modal -->
                    <button class="form-btn btn-danger" data-toggle="modal" data-target="#checkout-work-time-modal">
                        <i class="fa-solid fa-clock"></i><span>Checkout</span>
                    </button>

                </div>

            </section>

        </div>

    </div>

</div>

<?php require_once Helper\Path::build_path(PUBLIC_PATH, 'view', 'pages', 'inc', 'footer.php'); ?>