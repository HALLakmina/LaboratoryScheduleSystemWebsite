async function  loadPage (page) {
    try{
        const response = await fetch(`./Pages/${page}.html`)
        if (!response.ok) {
            throw new Error("Page not found: " + page);
        }
        const data = await response.text();

        document.getElementById("app-content").innerHTML = data;
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