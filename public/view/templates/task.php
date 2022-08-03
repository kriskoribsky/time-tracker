<div class="main-content">

    <h1>Tasks</h1>

    <section class="data-section shadow add-session">

        <h2>Add new tasks to your projects</h2>

        <form class="form" action="">

            <div class="form-row">
                <label for="project-selection">Project:</label>
                <select class="selection-dropdown" name="" id="project-selection">

                    <option value="">Choose a project</option>
                    <option value="">Jobin</option>
                    <option value="">Elcop</option>
                    <option value="">Elcop-prihlaska</option>
                    <option value="">MiniRelaxMier</option>

                </select>
            </div>

            <hr class="form-divide">

            <div class="form-row">

                <label>
                    <span>Task name: </span>
                    <input type="text">
                </label>

            </div>

            <div class="form-row">
                <button type="submit" class="form-btn btn-submit">
                    <i class="fa-solid fa-check"></i><span>Add</span>
                </button>
            </div>

            <div class="alert danger hidden" role="alert">Please choose a valid project and try again</div>

        </form>

    </section>

</div>