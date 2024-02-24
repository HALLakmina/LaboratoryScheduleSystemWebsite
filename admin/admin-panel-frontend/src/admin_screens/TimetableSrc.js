import React from 'react'
import SideNavBar from './SideNavBar'

function TimetableSrc() {
  return (
    <div className="d-flex row-1" style={{minHeight:"100vh"}}>
      <div className="d-flex col-2" style={{position: 'fixed'}}>
        <SideNavBar/>
      </div>
      <div className="col-12 d-flex justify-content-end">
        <div className="d-flex col-10  align-items-center flex-column mt-5 text-light mb-5">
        <div className="my-5">
                    <table className="table table-hover">
                        <thead className="thead-dark">
                            <tr>
                                <th className="px-3 text-center">Time</th>
                                <th className="px-3 text-center">Monday</th>
                                <th className="px-3 text-center">Tuesday</th>
                                <th className="px-3 text-center">Wednesday</th>
                                <th className="px-3 text-center">Thursday</th>
                                <th className="px-3 text-center">Friday</th>
                            </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td className="px-3 text-center"><p>8.00AM-9.00AM</p></td>

                            <td className="px-3 text-center">
                              <div className="row-1 d-flex justify-content-space-between">
                                <div className="col-9 d-flex"style={{flexDirection:"column", alignItems:"start"}}>
                                  <p>TICT1114</p>
                                  <p>Essential of ICT</p>
                                  <p>Lahiru</p>
                                </div>
                                <div className="clo-3"style={{display:"flex", justifyContent: 'end'}}>
                                  <div className="d-flex "style={{flexDirection:"column"}}>
                                    <div style={{backgroundColor:"purple", color:"white", fontWeight:"bold", fontSize:"10px",  borderRadius:"20%"}}>
                                      Active
                                    </div>
                                    <button className="mt-5 btn btn-primary">Edit</button>
                                  </div>
                                </div>
                              </div>
                            </td>

                            <td className="px-3 text-center">
                            <div className="row-1 d-flex justify-content-space-between">
                                <div className="col-9 d-flex"style={{flexDirection:"column", alignItems:"start"}}>
                                  <p>TICT1114</p>
                                  <p>Essential of ICT</p>
                                  <p>Lahiru</p>
                                </div>
                                <div className="clo-3"style={{display:"flex", justifyContent: 'end'}}>
                                  <div className="d-flex "style={{flexDirection:"column"}}>
                                    <div style={{backgroundColor:"purple", color:"white", fontWeight:"bold", fontSize:"10px",  borderRadius:"20%"}}>
                                      Active
                                    </div>
                                    <button className="mt-5 btn btn-primary">Edit</button>
                                  </div>
                                </div>
                              </div>
                            </td>
                            <td className="px-3 text-center">
                            <div className="row-1 d-flex justify-content-space-between">
                                <div className="col-9 d-flex"style={{flexDirection:"column", alignItems:"start"}}>
                                  <p>TICT1114</p>
                                  <p>Essential of ICT</p>
                                  <p>Lahiru</p>
                                </div>
                                <div className="clo-3"style={{display:"flex", justifyContent: 'end'}}>
                                  <div className="d-flex "style={{flexDirection:"column"}}>
                                    <div style={{backgroundColor:"purple", color:"white", fontWeight:"bold", fontSize:"10px",  borderRadius:"20%"}}>
                                      Active
                                    </div>
                                    <button className="mt-5 btn btn-primary">Edit</button>
                                  </div>
                                </div>
                              </div>
                            </td>
                            <td className="px-3 text-center">
                            <div className="row-1 d-flex justify-content-space-between">
                                <div className="col-9 d-flex"style={{flexDirection:"column", alignItems:"start"}}>
                                  <p>TICT1114</p>
                                  <p>Essential of ICT</p>
                                  <p>Lahiru</p>
                                </div>
                                <div className="clo-3"style={{display:"flex", justifyContent: 'end'}}>
                                  <div className="d-flex "style={{flexDirection:"column"}}>
                                    <div style={{backgroundColor:"purple", color:"white", fontWeight:"bold", fontSize:"10px",  borderRadius:"20%"}}>
                                      Active
                                    </div>
                                    <button className="mt-5 btn btn-primary">Edit</button>
                                  </div>
                                </div>
                              </div>
                            </td>
                            <td className="px-3 text-center">
                            <div className="row-1 d-flex justify-content-space-between">
                                <div className="col-9 d-flex"style={{flexDirection:"column", alignItems:"start"}}>
                                  <p>TICT1114</p>
                                  <p>Essential of ICT</p>
                                  <p>Lahiru</p>
                                </div>
                                <div className="clo-3"style={{display:"flex", justifyContent: 'end'}}>
                                  <div className="d-flex "style={{flexDirection:"column"}}>
                                    <div style={{backgroundColor:"purple", color:"white", fontWeight:"bold", fontSize:"10px",  borderRadius:"20%"}}>
                                      Active
                                    </div>
                                    <button className="mt-5 btn btn-primary">Edit</button>
                                  </div>
                                </div>
                              </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>  
        </div>
      </div>
    </div>
  )
}

export default TimetableSrc