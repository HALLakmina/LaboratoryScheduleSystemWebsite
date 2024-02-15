import React from 'react'

function FooterBar() {
    const footerStyle ={
        position:"fixed",
        bottom:"0",
        left:"0",
        right:"0",
        backgroundColor:"black",
        color:"white",
        height:"50px",
    }
  return (
    <footer className="d-flex justify-content-center align-item-center "style={footerStyle}>
        <p style={{marginTop:"1rem"}}>Laboratory Schedule System &copy; 2018ICTS39,2018ICTS##</p>
    </footer>
  )
}

export default FooterBar