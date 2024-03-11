import React from 'react';
import { useEffect, useState } from 'react';
import SideNavBar from './SideNavBar';
import axios from 'axios';

function LectureSrc() {
    const [data, setData] = useState([])
    const [takeLecture, setTakeLecture] = useState({id:'', name:'',userName:'',faculty:'',email:'',mobileNo:'',password:''});
    const [trigger, setTrigger] = useState(false);
    const [updateLecture, setUpdateLecture] = useState([]);


    useEffect (()=>{
    fetch('http://localhost:8081/lecture_details')
        .then(res => res.json())
        .then(data => setData(data))
        .catch(err => console.error(err)); 
    },[])

    function handleSubmit(e){
        e.preventDefault();
        axios.post('http://localhost:8081/lecture_details', {...takeLecture})
        .then(res=>{
            console.log(res);
            window.location.reload();
        }).catch(err=>console.log(err));
    }

    function handleDelete(id){
        axios.delete('http://localhost:8081/lecture_details/'+id)
        .then(res=>{
            window.location.reload();
        })
    }
    function handleUpdate(id, name, userName, faculty, email, mobileNo, password){
        setUpdateLecture([id, name, userName, faculty, email, mobileNo, password]);
        setTrigger(true);
    }
    return (
        <div className="d-flex row-1" style={{minHeight:"100vh"}}>
            <div className="d-flex col-2" style={{position: 'fixed'}}>
            <SideNavBar/>
            </div>
            <div className="col-11 d-flex justify-content-end">
                <div className="d-flex col-10  align-items-center flex-column mt-5 text-light mb-5">
                    <div style={{width:"400px", height:"auto"}} className="text-light">
                        <form className=" bg-dark p-4 d-flex flex-column rounded-5" onSubmit={handleSubmit}>
                            <div className="form-group mb-3">
                                <label htmlFor="exampleInputLectureID">Lecture ID</label>
                                <input 
                                    type="text"
                                    name="lectureId"
                                    className="form-control"
                                    id="exampleInputLectureID"
                                    onChange={(e)=>setTakeLecture({...takeLecture, id: e.target.value})}
                                />
                            </div>
                            <div className="form-group mb-3">
                                <label htmlFor="exampleInputLectureName">Lecture Name</label>
                                <input 
                                    type="text"
                                    name="lectureName"
                                    className="form-control"
                                    id="exampleInputLectureName"
                                    
                                    onChange={(e)=>setTakeLecture({...takeLecture, name: e.target.value})}
                                />
                            </div>
                            <div className="form-group mb-3">
                                <label htmlFor="exampleInputUserName">User Name</label>
                                <input 
                                    type="text"
                                    name="userName"
                                    className="form-control"
                                    id="exampleInputUserName"
                                    
                                    onChange={(e)=>setTakeLecture({...takeLecture, userName: e.target.value})}
                                />
                            </div>
                            <div className="form-group mb-3">
                                <label htmlFor="exampleInputFaculty">Faculty</label>
                                <input 
                                    type="text"
                                    name="faculty"
                                    className="form-control"
                                    id="exampleInputFaculty"
                                    
                                    onChange={(e)=>setTakeLecture({...takeLecture, faculty: e.target.value})}
                                />
                            </div>
                            <div className="form-group mb-3">
                                <label htmlFor="exampleInputEmail">Email</label>
                                <input 
                                    type="text"
                                    name="email"
                                    className="form-control"
                                    id="exampleInputEmail"
                                    onChange={(e)=>setTakeLecture({...takeLecture, email: e.target.value})}
                                />
                            </div>
                            <div className="form-group mb-3">
                                <label htmlFor="exampleInputMobileNo">Mobil No</label>
                                <input 
                                    type="text"
                                    name="mobileNo"
                                    className="form-control"
                                    id="exampleInputMobileNo"
                                    onChange={(e)=>setTakeLecture({...takeLecture, mobileNo: e.target.value})}
                                />
                            </div>
                            <div className="form-group mb-3">
                                <label htmlFor="exampleInputPassword">Password</label>
                                <input 
                                    type="password"
                                    name="password"
                                    className="form-control"
                                    id="exampleInputPassword"
                                    
                                    onChange={(e)=>setTakeLecture({...takeLecture, password: e.target.value})}
                                />
                            </div>
                                <input 
                                    type="submit"
                                    name="upload"
                                    value="UPLOAD"
                                    className="btn btn-primary"
                                />
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
                                    <th className="px-3 text-center">Email</th>
                                    <th className="px-3 text-center">Mobil No</th>
                                    <th className="px-3 text-center">Password</th>
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
                                <td className="px-3 text-center">{d.email}</td>
                                <td className="px-3 text-center">{d.mobil_number}</td>
                                <td className="px-3 text-center">{d.password}</td>
                                <td className="px-3 d-flex justify-content-center">
                                    <button className='btn btn-primary m-2 px-4' onClick={()=>handleUpdate(d.lecture_id, d.full_name, d.user_name, d.faculty,d.email, d.mobil_number, d.password)}>Update</button><br></br>
                                    <button className='btn btn-danger m-2 px-4' onClick={()=>handleDelete(d.lecture_id)}>Delete</button>
                                </td>
                                
                            </tr>
                            ))}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <UpdateLecture triggerOpen={trigger} triggerClose={setTrigger} defaultLecture={updateLecture}/>
        </div>
    )
}

export default LectureSrc

function UpdateLecture(props){

    const [newUpdateLecture, setNewUpdateLecture, ] = useState({newId:'', newName:'', newUserName:'', newFaculty:'', newEmail:'', newMobileNo:'',newPassword:''})
        
    useEffect(()=>{
        setNewUpdateLecture({newName:props.defaultLecture[1], newUserName:props.defaultLecture[2], newFaculty:props.defaultLecture[3], newEmail:props.defaultLecture[4], newMobileNo:props.defaultLecture[5], newPassword:props.defaultLecture[6]})
    },[props.defaultLecture])

    function handleUpdate(e){
        e.preventDefault();
        axios.put('http://localhost:8081/lecture_details/'+props.defaultLecture[0], {...newUpdateLecture})
        .then(res=>{
            console.log(res);
            window.location.reload();
        }).catch(err=>console.log(err));
    }
    const popupStyle={
        main:{
            position: 'fixed',
            margin:0,
            padding:0,
            width:'100%', 
            height:'100%', 
            backgroundColor:'rgba(255,255,255,0.4)',
            display:'flex',
            justifyContent:'center',
            alignItems: 'center',
        },
    };
    return (props.triggerOpen)?(
        <div style={popupStyle.main}>
            <div>
                <div>
                    <button onClick={()=>props.triggerClose(false)}>Close</button>
                    <h1>{props.defaultLecture[0]}</h1>
                </div>

                <div style={{width:"400px", height:"auto"}} className="text-light">
                    <form className=" bg-dark p-4 d-flex flex-column rounded-5" onSubmit={handleUpdate}>
                        <div className="form-group mb-3">
                            <label htmlFor="newInputName">Lecture Name</label>
                            <input
                                type="text"
                                name="newName"
                                className="form-control"
                                id="newInputName"
                                defaultValue={props.defaultLecture[1]}
                                onChange={(e)=>setNewUpdateLecture({...newUpdateLecture, newName:e.target.value})}
                            />
                        </div>
                        <div className="form-group mb-3">
                            <label htmlFor="newInputUserName">User Name</label>
                            <input
                                type="text"
                                name="newName"
                                className="form-control"
                                id="newInputUserName"
                                defaultValue={props.defaultLecture[2]}
                                onChange={(e)=>setNewUpdateLecture({...newUpdateLecture, newUserName:e.target.value})}
                            />
                        </div>
                        <div className="form-group mb-3">
                            <label htmlFor="newInputFaculty">Faculty</label>
                            <input
                                type="text"
                                name="newName"
                                className="form-control"
                                id="newInputFaculty"
                                defaultValue={props.defaultLecture[3]}
                                onChange={(e)=>setNewUpdateLecture({...newUpdateLecture, newFaculty:e.target.value})}
                            />
                        </div>
                        <div className="form-group mb-3">
                            <label htmlFor="newInputEmail">Email</label>
                            <input
                                type="text"
                                name="newName"
                                className="form-control"
                                id="newInputEmail"
                                defaultValue={props.defaultLecture[4]}
                                onChange={(e)=>setNewUpdateLecture({...newUpdateLecture, newEmail:e.target.value})}
                            />
                        </div>
                        <div className="form-group mb-3">
                            <label htmlFor="newInputMobileNo">Mobile Number</label>
                            <input
                                type="number"
                                name="newName"
                                className="form-control"
                                id="newInputMobileNo"
                                defaultValue={props.defaultLecture[5]}
                                onChange={(e)=>setNewUpdateLecture({...newUpdateLecture, newMobileNo:e.target.value})}
                            />
                        </div>
                        <div className="form-group mb-3">
                            <label htmlFor="newInputPassword">Password</label>
                            <input
                                type="password"
                                name="newName"
                                className="form-control"
                                id="newInputPassword"
                                defaultValue={props.defaultLecture[6]}
                                onChange={(e)=>setNewUpdateLecture({...newUpdateLecture, newPassword:e.target.value})}
                            />
                        </div>
                        <input type="submit" name="upload" value="UPLOAD" className="btn btn-primary"/>
                    </form>
                </div>
            </div>
        </div>
    ): "";
}