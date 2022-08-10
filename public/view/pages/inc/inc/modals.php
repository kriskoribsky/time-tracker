<!-- modal create project group -->
<div class="modal" id="create-group-modal" style="display: none;">
    <div class="modal-dialog" role="document">
        <form action="/forms" method="POST" class="modal-content form">

            <div class="modal-header">
                
                <h5 class="modal-title">Create new project group</h5>

                <a type="button" class="modal-close-btn" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </a>

            </div>

            <div class="modal-body">

                <!-- <p>Project groups work as a collection of all your projects. All statistics and data about working-time displayed on site belong to the group you create here & select.</p> -->
                <p class="modal-comment">All statistics and data about working-time displayed later will belong to the group you create here.</p>

                <p>
                    <label>
                        <span>Group name: </span>
                        <input name="group-name" type="text" autofocus required>
                    </label>
                </p>

                <p class="color-input">
                    <label>
                        <span>Group color: </span>
                        <input class="form-color-pick" type="color" name="group-color" value="#4e73df" required>
                    </label>
                </p>

            </div>

            <div class="modal-footer">

                <a class="form-btn modal-btn btn-secondary" data-dismiss="modal">
                    <span>Close</span>
                </a>

                <button class="form-btn modal-btn  btn-submit" type="submit" name="submit" value="new-group">
                    <span>Create</span>
                </button>

            </div>

        </form>
    </div>
</div>

<!-- modal add project -->
<div class="modal" id="create-project-modal" style="display: none;">
    <div class="modal-dialog" role="document">
        <form action="/forms" method="POST" class="modal-content form">

            <div class="modal-header">
                
                <h5 class="modal-title">Create new project</h5>

                <a type="button" class="modal-close-btn" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </a>

            </div>

            <div class="modal-body">

                <p>
                    <label>
                        <span>Project name: </span>
                        <input name="project-name" type="text" autofocus required>
                    </label>
                </p>

            </div>

            <div class="modal-footer">

                <a class="form-btn modal-btn btn-secondary" data-dismiss="modal">
                    <span>Close</span>
                </a>

                <button class="form-btn modal-btn  btn-primary" type="submit" name="submit" value="new-project">
                    <span>Save project</span>
                </button>

            </div>

        </form>
    </div>
</div>

<!-- modal checkout working hours -->
<div class="modal" id="checkout-work-time-modal" style="display: none;">
    <div class="modal-dialog" role="document">
        <form action="" method="POST" class="modal-content form">

            <div class="modal-header">
                
                <h5 class="modal-title">Checkout unpaid work-time</h5>

                <a type="button" class="modal-close-btn" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </a>

            </div>

            <div class="modal-body">

                <div class="alert warning" role="alert">
                    Warning! This action will reset your unpaid work-time across <strong>all projects.</strong><br><br>
                    Are you sure you want to continue?
                </div>


            </div>

            <div class="modal-footer">

                <a class="form-btn modal-btn btn-secondary" data-dismiss="modal">
                    <span>Close</span>
                </a>

                <button type="submit" class="form-btn modal-btn btn-danger">
                    <i class="fa-solid fa-clock"></i><span>Checkout unpaid time</span>
                </button>

            </div>

        </form>
    </div>
</div>

<!-- modal failed db insert (duplicate) -->
<?php if (isset($_GET['m']) && $_GET['m'] === 'duplicate'): ?>

<div class="modal modal-show" id="" style="display: block;">
    <div class="modal-dialog" role="document">
        <form action="<?php echo Sanitize::sanitize_html(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)); ?>" method="POST" class="modal-content form">

            <div class="modal-header">
                
                <h5 class="modal-title">Duplicate</h5>

                <a type="button" class="modal-close-btn" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </a>

            </div>

            <div class="modal-body">

                <div class="alert danger" role="alert">
                    <?php echo ucfirst($_GET['o']); ?> with the same name already exists.
                </div>


            </div>

            <div class="modal-footer">

                <button type="submit" class="form-btn modal-btn btn-secondary" data-dismiss="modal">
                    <span>Close</span>
                </button>

            </div>

        </form>
    </div>
</div>

<?php endif; ?>

<!-- modal failed db insert (other error) -->
<?php if (isset($_GET['m']) && $_GET['m'] === 'failed'): ?>

<div class="modal modal-show" id="" style="display: block;">
    <div class="modal-dialog" role="document">
        <form action="<?php echo Sanitize::sanitize_html(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)); ?>" method="POST" class="modal-content form">

            <div class="modal-header">
                
                <h5 class="modal-title">Ooops!</h5>

                <a type="button" class="modal-close-btn" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </a>

            </div>

            <div class="modal-body">

                <div class="alert danger" role="alert">
                    There has been an error when trying to save your data.
                </div>

            </div>

            <div class="modal-footer">

                <button type="submit" class="form-btn modal-btn btn-secondary" data-dismiss="modal">
                    <span>Close</span>
                </button>

            </div>

        </form>
    </div>
</div>

<?php endif; ?>

<!-- modal succes db insert -->
<?php if (isset($_GET['m']) && $_GET['m'] === 'ok'): ?>

<div class="modal modal-show" id="" style="display: block;">
    <div class="modal-dialog" role="document">
        <form action="<?php echo Sanitize::sanitize_html(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)); ?>" method="POST" class="modal-content form">

            <div class="modal-header">
                
                <h5 class="modal-title">Hurray!</h5>

                <a type="button" class="modal-close-btn" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </a>

            </div>

            <div class="modal-body">

                <div class="alert success" role="alert">
                    <?php echo ucfirst($_GET['o']); ?> added successfully.
                </div>

            </div>

            <div class="modal-footer">

                <button type="submit" type class="form-btn modal-btn btn-secondary" data-dismiss="modal">
                    <span>Close</span>
                </button>

            </div>

        </form>
    </div>
</div>

<?php endif; ?>

<!-- modal failed db insert (wrong data format) -->
<?php if (isset($_GET['m']) && $_GET['m'] === 'wrong-format'): ?>

<div class="modal modal-show" id="" style="display: block;">
    <div class="modal-dialog" role="document">
        <form action="<?php echo Sanitize::sanitize_html(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)); ?>" method="POST" class="modal-content form">

            <div class="modal-header">
                
                <h5 class="modal-title">Wrong format!</h5>

                <a type="button" class="modal-close-btn" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </a>

            </div>

            <div class="modal-body">

                <div class="alert danger" role="alert">
                    The data you entered has an incorrect format.
                </div>

            </div>

            <div class="modal-footer">

                <button type="submit" type class="form-btn modal-btn btn-secondary" data-dismiss="modal">
                    <span>Close</span>
                </button>

            </div>

        </form>
    </div>
</div>

<?php endif; ?>