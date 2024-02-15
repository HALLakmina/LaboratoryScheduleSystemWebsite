/* eslint-disable jsx-a11y/anchor-has-content */
/* eslint-disable jsx-a11y/anchor-is-valid */
import React from 'react'
import { Link } from 'react-router-dom'

export default function SideNavBar() {
  return (
    <div className="container-fluid">
      <div className="row flex-nowrap">
        <div className="bg-dark col-auto col-ma-2 min-vh-100">
          <div className="bg-dark p-2">
            
            <ul className="nav nav-pills flex-column mt-4">
              <li className="nav-item"><Link className="nav-link" to="/HomeSrc" >Home</Link></li>
              <li className="nav-item"><Link className="nav-link" to="/AdminSrc" >Admin</Link></li>
              <li className="nav-item"><Link className="nav-link" to="/SubjectSrc" >Subject</Link></li>
              <li className="nav-item"><Link className="nav-link" to="/LectureSrc" >Lecture</Link></li>
              <li className="nav-item"><Link className="nav-link" to="/TimetableSrc" >Time Table</Link></li>
              <li className="nav-item"><Link className="nav-link" to="/NewsSrc" >News</Link></li>
            </ul>

          </div>

        </div>

      </div>
      
    </div>
  )
}
