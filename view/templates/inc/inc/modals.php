<!-- modal add project -->
<div class="modal" id="create-project-modal" style="display: none;">
    <div class="modal-dialog" role="document">
        <form aciton="" class="modal-content form">

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
                        <input type="text" autofocus>
                    </label>
                </p>

            </div>

            <div class="modal-footer">

                <a class="form-btn modal-btn btn-secondary" data-dismiss="modal">
                    <span>Close</span>
                </a>

                <button class="form-btn modal-btn  btn-primary" type="submit">
                    <span>Save project</span>
                </button>

            </div>

        </form>
    </div>
</div>

<!-- modal checkout working hours -->
<div class="modal" id="checkout-work-time-modal" style="display: none;">
    <div class="modal-dialog" role="document">
        <form aciton="" class="modal-content form">

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