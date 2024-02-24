import React from 'react'
import { useEffect, useState } from 'react';
import SideNavBar from './SideNavBar'

function LectureSrc() {
    const [data, setData] = useState([])
  useEffect (()=>{
    fetch('http://localhost:8081/lecture_details')
    .then(res => res.json())
    .then(data => setData(data))
    .catch(err => console.error(err)); 
  },[])
  return (
    <div className="d-flex row-1" style={{minHeight:"100vh"}}>
      <div className="d-flex col-2" style={{position: 'fixed'}}>
        <SideNavBar/>
      </div>
        <div className="col-12 d-flex justify-content-end">
            <div className="d-flex col-10  align-items-center flex-column mt-5 text-light mb-5">
                <div style={{width:"400px", height:"auto"}} className="text-light">
                    <form className=" bg-dark p-4 d-flex flex-column rounded-5">
                        <div className="form-group mb-3">
                            <label for="exampleInputLectureID">Lecture ID</label>
                            <input type="text" name="lectureId" class="form-control" id="exampleInputLectureID"></input>
                        </div>
                        <div className="form-group mb-3">
                            <label for="exampleInputLectureName">Lecture Name</label>
                            <input type="text" name="lectureName" class="form-control" id="exampleInputLectureName"></input>
                        </div>
                        <div className="form-group mb-3">
                            <label for="exampleInputUserName">User Name</label>
                            <input type="text" name="userName" class="form-control" id="exampleInputUserName"></input>
                        </div>
                        <div className="form-group mb-3">
                            <label for="exampleInputFaculty">Faculty</label>
                            <input type="text" name="faculty" class="form-control" id="exampleInputFaculty"></input>
                        </div>
                        <div className="form-group mb-3">
                            <label for="exampleInputEmail">Email</label>
                            <input type="text" name="email" class="form-control" id="exampleInputEmail"></input>
                        </div>
                        <div className="form-group mb-3">
                            <label for="exampleInputMobileNo">Mobil No</label>
                            <input type="text" name="mobileNo" class="form-control" id="exampleInputMobileNo"></input>
                        </div>
                        <div className="form-group mb-3">
                            <label for="exampleInputPassword">Password</label>
                            <input type="text" name="password" class="form-control" id="exampleInputPassword"></input>
                        </div>
                            <input type="submit" name="upload" value="Upload" class="btn btn-primary"></input>
                    </form>
                        
                </div>
                <div className="my-5">
                    <table className="table table-hover">
                        <thead className="thead-dark">
                            <tr>
                                <th className="px-3 text-center">Lecture ID</th>
                                <th className="px-3 text-center">Lecture Name</th>
                                <th className="px-3 text-center">User Name</th>
                                <th className="px-3 text-center">Faculty</th>
                                {/* <th className="px-3 text-center">Email</th> */}
                                <th className="px-3 text-center">Mobil No</th>
                                {/* <th className="px-3 text-center">Password</th> */}
                                <th className="px-3 text-center">Edit</th>
                            </tr>
                        </thead>
                        <tbody>
                        {data.map((d, i) =>(
                        <tr key={i}> 
                            <td className="px-3 text-center">{d.lecture_id}</td>
                            <td className="px-3 text-center">{d.full_name}</td>
                            <td className="px-3 text-center">{d.user_name}</td>
                            <td className="px-3 text-center">{d.faculty}</td>
                            {/* <td className="px-3 text-center">{d.lecture_id}</td> */}
                            <td className="px-3 text-center">{d.mobil_number}</td>
                            {/* <td className="px-3 text-center">{d.lecture_id}</td> */}
                            <td className="px-3 d-flex justify-content-center">
                                <button className='btn btn-primary m-2 px-4'>Update</button><br></br>
                                <button className='btn btn-danger m-2 px-4'>Delete</button>
                            </td>
                            
                        </tr>
                        ))}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
  )
}

export default LectureSrc