<div class="main-content">

    <h1>Sessions</h1>

    <section class="data-section shadow add-session">

        <h2>Track new working session</h2>

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

            <div class="form-row">
                <label for="task-selection">Task:</label>
                <select class="selection-dropdown" name="" id="task-selection">

                    <option value="">Choose a task</option>
                    <option value="">Searcher</option>
                    <option value="">Price list</option>
                    <option value="">Registry form</option>

                </select>
            </div>

            <hr class="form-divide">

            <div class="form-row">

                <label>
                    <span>Started: </span>
                    <input class="time-input" type="text" placeholder="11:00">
                </label>
                
                <label>
                    <span>Finished: </span>
                    <input class="time-input" type="text" placeholder="17:45">
                </label>
                
            </div>

            <div class="form-row">
                <button type="submit" class="form-btn btn-submit">
                    <i class="fa-solid fa-check"></i><span>Submit</span>
                </button>
            </div>

            <div class="alert danger hidden" role="alert">Please enter the correct time format and try again.</div>

        </form>


    </section>

</div>