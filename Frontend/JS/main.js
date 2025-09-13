async function loadAppPage(){
    const response = await fetch('App.html')
    try{
        if (!response.ok) {
            throw new Error("Page not found: App.html");
        }
        const data  = await response.text();
        document.getElementById("index-content").innerHTML = data;   
    }
    catch(error){
      document.getElementById("content").innerHTML = `<p style="color:red;">${error}</p>`;
    };
}

async function loadFooterBar(){
    try{
        const response = await fetch('./Components/FooterBar.html')
        if (!response.ok) {
            throw new Error("Page not found: FooterBar.html");
        }
        const data =  await response.text();
        document.body.insertAdjacentHTML("beforeend", data);
    }
    catch(error){
        document.getElementById("content").innerHTML = `<p style="color:red;">${error}</p>`;
    }
}

async function loadNavigationBar (){
    try{
        const response = await fetch('./Components/NavigationBar.html')
        if (!response.ok) {
            throw new Error("Page not found: NavigationBar.html");
        }
        const data = await response.text()
        document.getElementById("nav-content").innerHTML = data;
    }
    catch(error) {
      document.getElementById("content").innerHTML = `<p style="color:red;">${error}</p>`;
    };
}

async function  loadPage (page, addToHistory=false ) {
    try{
        const response = await fetch(`./Pages/${page}.html`)
        if (!response.ok) {
            throw new Error("Page not found: " + page);
        }
        const data = await response.text();
        document.getElementById("app-content").innerHTML = data;

        // Change the URL (without reloading page)
        if (addToHistory) {
            history.pushState({ page }, "", page.replace(".html", ""));
        }
    }
    catch(error){
        document.getElementById("content").innerHTML = `<p style="color:red;">${error}</p>`;
    };
}    

function pageNavigation(event, page){
    event.preventDefault();
    window.location.hash = page;
    loadPage(window.location.hash, true)
}

window.addEventListener("popstate", (event) => {
  if (event.state && event.state.page) {
    loadPage(event.state.page);
  } else {
    loadPage("Home");
  }
});

// Load content when hash changes
window.addEventListener("hashchange", () => {
  const hash = location.hash.substring(1);
  if (hash === "Home") loadPage("Home");
  else if (hash === "Timetable") loadPage("Timetable");
  else if (hash === "News") loadPage("News");
});

// Initial load
window.addEventListener("DOMContentLoaded", () => {
  if (!location.hash) {
    location.hash = "Home";
  } else {
    window.dispatchEvent(new Event("hashchange"));
  }
});

window.pageNavigation = pageNavigation
loadAppPage()
loadNavigationBar()
loadFooterBar()