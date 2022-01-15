document.addEventListener("DOMContentLoaded", function(event) {
    // Left Navbar Toggle Show / Hide
    const toggle = document.getElementById('header-toggle');
    const nav = document.getElementById('nav-bar');
    const bodypd = document.getElementById('body-pd');
    const headerpd = document.getElementById('header');
    if(toggle && nav && bodypd && headerpd){
        toggle.addEventListener('click', toggleNavBar);
    }
    /** @public
     * Show / Hide website left navbar if user clicks it.
     * */
    function toggleNavBar(event) {
        nav.classList.toggle('show');
        toggle.classList.toggle('bx-x');
        bodypd.classList.toggle('body-pd');
        headerpd.classList.toggle('body-pd');
    }

    // Register nav link
    const linkColor = document.querySelectorAll('.nav_link')

    function colorLink(){
        if(linkColor){
            linkColor.forEach(l => l.classList.remove('active'));
            this.classList.add('active');
        }
    }
    linkColor.forEach(l=> l.addEventListener('click', colorLink));

    // Register Tasks events
    let jsTaskBox = document.querySelectorAll(".jsTaskBox");
    if (!jsTaskBox) jsTaskBox = []; // make it a iterables and avoids errors all together
    jsTaskBox.forEach(task => task.addEventListener('click', taskOnClick));
    const TOOLBAR_HIDE = "screen--toolbar-hide";
    const TASK_SELECTED = "selected";
    let toolBarSelected = null;
    let taskSelected = null;

    // Task even handle
    function taskOnClick(event) {
        let taskID = this.dataset.taskId;
        let taskToolBar = document.getElementById(`taskToolbar-${taskID}`);

        this.classList.add(TASK_SELECTED);
        if (taskSelected) taskSelected.classList.remove(TASK_SELECTED);
        taskSelected = this;

        if (taskToolBar) {
            taskToolBar.classList.toggle(TOOLBAR_HIDE);
        }
        if (toolBarSelected && toolBarSelected !== taskToolBar) {
            toolBarSelected.classList.toggle(TOOLBAR_HIDE);
        }

        if (toolBarSelected && toolBarSelected === taskToolBar) {
            toolBarSelected =  null;
        } else {
            toolBarSelected = taskToolBar;
        }
    }

    const profileDropDown = document.getElementById('profileDropDown');
    const profileDropDownWindow = document.getElementById('profileDropDownWindow');
    if (profileDropDown && profileDropDownWindow) {
        profileDropDown.addEventListener('click', (ev) => {
            profileDropDownWindow.classList.toggle('show');
            setTimeout(() => { // Makes sure that the window will closes if the uses dont click again
                profileDropDownWindow.classList.remove('show');
            }, 3000);

        });
    }

});