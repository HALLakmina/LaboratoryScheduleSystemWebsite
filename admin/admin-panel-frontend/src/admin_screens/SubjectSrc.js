import React, { useEffect, useState } from 'react'
import SideNavBar from './SideNavBar'

function SubjectSrc() {
    const [subjectData, setSubjectData] = useState([]);
    useEffect(()=>{
        fetch('http://localhost:8081/practical_subject')
        .then(res=>res.json())
        .then(data => setSubjectData(data))
        .catch(err=>console.error(err));
    }, [])

  return (
    <div className="d-flex row-1" style={{minHeight:"100vh"}}>
        <div className="" style={{position: 'fixed'}}>
            <SideNavBar/>
        </div>
        <div className="col-12 d-flex justify-content-end">
            <div className="d-flex col-10  align-items-center flex-column mt-5 text-light mb-5">
                <div style={{width:"400px", padding:"30px", height:"auto"}} className=" text-light ">
                    <form className=" bg-dark p-4 d-flex flex-column rounded-5">
                        <div className="form-group mb-3">
                            <label for="exampleInputSubjectCode">Subject Code</label>
                            <input type="text" name="subjectCode" class="form-control" id="exampleInputSubjectCode"></input>
                        </div>
                        <div className="form-group mb-3">
                            <label for="exampleInputSubjectName">SubjectName</label>
                            <input type="text" name="subjectName" class="form-control" id="exampleInputSubjectName"></input>
                        </div>
                        <div className="form-group mb-3">
                            <label for="exampleInputLectureID">Lecture ID</label>
                            <input type="text" name="lectureID" class="form-control" id="exampleInputLectureID"></input>
                        </div>
                        <div className="form-group mb-3">
                            <label for="exampleInputYear">Year</label>
                            <input type="text" name="year" class="form-control" id="exampleInputYear"></input>
                        </div>
                            <input type="submit" name="upload" value="Upload" class="btn btn-primary"></input>
                    </form>
                        
                </div>
                <div className="my-5">
                    <table className="table table-hover">
                        <thead className="thead-dark">
                            <tr>
                                <th className="px-3 text-center">SubjectCode</th>
                                <th className="px-3 text-center">Subject Name</th>
                                <th className="px-3 text-center">Lecture ID</th>
                                <th className="px-3 text-center">Year</th>
                                <th className="px-3 text-center">Edit</th>
                            </tr>
                        </thead>
                        <tbody>
                        {subjectData.map((d, i)=>(
                            <tr key={i}>
                                <td className="px-3 text-center">{d.Subject_cord}</td>
                                <td className="px-3 text-center">{d.Subject}</td>
                                <td className="px-3 text-center">{d.lecture_id}</td>
                                <td className="px-3 text-center">{d.year}</td>
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

export default SubjectSrc