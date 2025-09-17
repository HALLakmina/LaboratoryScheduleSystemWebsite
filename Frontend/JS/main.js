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


loadAppPage()
loadNavigationBar()
loadFooterBar()