/* eslint-disable jsx-a11y/alt-text */
import React from 'react'
import SideNavBar from './SideNavBar'

export default function HomeSrc() {
  return (
    <div className="d-flex row-1" style={{minHeight:"100vh"}}>
      <div className="d-flex col-2">
        <SideNavBar/>
      </div>
      <div className=" col-10 ">
        <div className="d-flex justify-content-end align-item-center  flex-row" style={{alignItems:"center"}}>
          <img src="/img/website_image/Vavuniversity.png" style={{width:"200px", height:"200px", borderRadius:"100%"}}></img>
          <h1 style={{fontSize:"70px", fontWeight:"bold", textShadow:"0 8px 8px rgba(0,0,0,0.9", color:"#fff", margin:"200px 50px 100px 50px"}}>Laboratory Schedule <br></br> System</h1>
        </div>
        <div className="text-light ms-5">
          <h1 style={{textDecorationLine:"underline", textDecorationColor:"red"}}>About Us</h1>
          <p>
            Lorem ipsum dolor sit amet consectetur adipisicing elit. Et nulla, perferendis repudiandae<br></br>
            cum a voluptates at quasi quas, minus architecto quae illum dolore vero magni quos<br></br>
            veritatis velit enim sapiente?
          </p>
        </div>
      </div>
        
        
    </div>
  )
}
