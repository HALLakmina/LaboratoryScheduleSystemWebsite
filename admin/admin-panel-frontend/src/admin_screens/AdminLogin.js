/* eslint-disable jsx-a11y/alt-text */
import React from "react";

export default function AdminLogin() {
  return (
    <div className="container d-flex row-cols-1"style={{minHeight:"100vh",alignItems:"center",}}>
      <div className="ms-5 mt-5 col-6">
        <img src="/img/website_image/Vavuniversity.png" style={{width:"200px", height:"200px", borderRadius:"100%"}}></img>
        <h1 className="text-light" style={{textShadow:"0 8px 8px rgba(0,0,0,0.9", fontSize:"50px"}}>Laboratory Schedule <br></br>System</h1>
      </div>


      <form className="d-flex justify-content-center col-6">
        <div className=" bg-light text-black  container pt-5 pb-5 mt-5  rounded col-6 d-flex justify-content-center row me-5" style={{width:"600px"}}>
          <h1 className="text-center">Admin Login</h1>

          <div className="p-3">
            <label className="pe-2 text-center  col-4">User Name</label>
            <input type="text" name="adminName" className=" col-6" style={{outlineColor:"rgba(25,135,84,0.6)", border:"none", backgroundColor:"none", borderBottom:"solid red 3px"}}></input>
          </div>

          <div className="p-3">
            <label className="pe-2 text-center  col-4">Password</label>
            <input type="password" name="adminPassword" className=" col-6" style={{outlineColor:"rgba(25,135,84,0.6)", border:"none", backgroundColor:"none", borderBottom:"solid red 3px"}}></input>
          </div>

          <input type="submit" name="AdminLogin" className="btn btn-dark col-8 mt-3" value="Login"></input>
        </div>
      </form>
    </div>
  );
}
