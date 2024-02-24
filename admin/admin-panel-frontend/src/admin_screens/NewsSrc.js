import React, { useEffect, useState } from "react";
import SideNavBar from "./SideNavBar";
//import axios from "axios";
function NewsSrc() {
    const [newsData, setNewsData] = useState([])
    useEffect(()=>{
        fetch('http://localhost:8081/news')
        .then(res => res.json())
        .then(data => setNewsData(data))
        .catch(err=> console.error(err));   
    }, [])

  return (
    <div className="d-flex row-1" style={{minHeight:"100vh"}}>
        <div className="d-flex col-2" style={{position: 'fixed'}}>
            <SideNavBar />
        </div>
        <div className="col-12 d-flex justify-content-end">
            <div className="d-flex col-10  align-items-center flex-column mt-5 text-light mb-5">
            <div style={{width:"400px", height:"auto"}} className="text-light">
                <form className=" bg-dark p-4 d-flex flex-column rounded-5">
                    <div className="form-group mb-3">
                        <label for="exampleInputTitle">Title</label>
                        <input type="text" name="title" class="form-control" id="exampleInputTitle"></input>
                    </div>
                    <div className="form-group mb-3">
                        <label for="exampleInputImage">Image</label>
                        <input type="file" name="image" class="form-control" id="exampleInputImage"></input>
                    </div>
                    <div className="form-group mb-3">
                        <label for="exampleInputDescription">Description</label>
                        <input type="text" name="description" class="form-control" id="exampleInputDescription"></input>
                    </div>
                    <div className="form-group mb-3">
                        <label for="exampleInputLocation">Location</label>
                        <input type="text" name="location" class="form-control" id="exampleInputLocation"></input>
                    </div>
                    <div className="form-group mb-3">
                        <label for="exampleInputTime">Time</label>
                        <input type="time" name="time" class="form-control" id="exampleInputTime"></input>
                    </div>
                    <div className="form-group mb-3">
                        <label for="exampleInputDate">Date</label>
                        <input type="date" name="date" class="form-control" id="exampleInputDate"></input>
                    </div>
                        <input type="submit" name="upload" value="Upload" class="btn btn-primary"></input>
                </form>
                    
            </div>
            <div className="my-5">
                <table className="table table-hover">
                    <thead className="thead-dark">
                        <tr>
                            <th className="px-3 text-center">Title</th>
                            <th className="px-3 text-center">Image</th>
                            <th className="px-3 text-center">Description</th>
                            <th className="px-3 text-center">Location</th>
                            <th className="px-3 text-center">Time</th>
                            <th className="px-3 text-center">Date</th>
                            <th className="px-3 text-center">Edit</th>
                        </tr>
                    </thead>
                    <tbody>
                    {newsData.map((d, i)=>(
                    <tr key={i} >
                        <td className="px-3 text-center">{d.title}</td>
                        <td className="px-3 text-center">{d.image}</td>
                        <td className="px-3 text-center">{d.description}</td>
                        <td className="px-3 text-center">{d.place}</td>
                        <td className="px-3 text-center">{d.time}</td>
                        <td className="px-3 text-center">{d.date}</td>
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
  );
}

export default NewsSrc;
