import React, { useEffect, useState } from 'react'
import SideNavBar from './SideNavBar'
import axios from 'axios';

function SubjectSrc() {
    const [subjectData, setSubjectData] = useState([]);
    const [takeSubject, setTakenSubject] = useState({code:'', name:'', lectureID:'', year:''});
    const [trigger, setTrigger] = useState(false);
    const [updateSubject, setUpdateSubject] = useState([]);
    useEffect(()=>{
        fetch('http://localhost:8081/practical_subject')
        .then(res=>res.json())
        .then(data => setSubjectData(data))
        .catch(err=>console.error(err));
    }, [])

    function handleSubmit(e){
        e.preventDefault();
        axios.post('http://localhost:8081/practical_subject', {...takeSubject})
        .then(res=>{
            console.log(res);
            window.location.reload();
        }).catch(err=>console.log(err));
    }

    function handleDelete(cord){
        axios.delete('http://localhost:8081/practical_subject/'+cord)
        .then(res=>{
            window.location.reload()
        })
    }
    
    function handleUpdate(updateCode, updateName, updateLecture, updateYear){
        setUpdateSubject([updateCode, updateName, updateLecture, updateYear]);
        setTrigger(true); 
        console.log(updateCode, updateName, updateLecture, updateYear);
    }
  return (
    <div className="d-flex row-1" style={{minHeight:"100vh"}}>
        <div className="" style={{position: 'fixed'}}>
            <SideNavBar/>
        </div>
        <div className="col-12 d-flex justify-content-end">
            <div className="d-flex col-10  align-items-center flex-column mt-5 text-light mb-5">
                <div style={{width:"400px", padding:"30px", height:"auto"}} className=" text-light ">
                    <form className=" bg-dark p-4 d-flex flex-column rounded-5" onSubmit={handleSubmit}>
                        <div className="form-group mb-3">
                            <label htmlFor="exampleInputSubjectCode">Subject Code</label>
                            <input
                                type="text"
                                name="subjectCode"
                                className="form-control"
                                id="exampleInputSubjectCode"
                                onChange={(e)=>setTakenSubject({...takeSubject, code:e.target.value})}
                            />
                        </div>
                        <div className="form-group mb-3">
                            <label htmlFor="exampleInputSubjectName">SubjectName</label>
                            <input
                                type="text"
                                name="subjectName"
                                className="form-control"
                                id="exampleInputSubjectName"
                                onChange={(e)=>setTakenSubject({...takeSubject, name:e.target.value})}
                            />
                        </div>
                        <div className="form-group mb-3">
                            <label htmlFor="exampleInputLectureID">Lecture ID</label>
                            <input
                                type="text"
                                name="lectureID"
                                className="form-control"
                                id="exampleInputLectureID"
                                onChange={(e)=>setTakenSubject({...takeSubject, lectureID:e.target.value})}
                            />
                        </div>
                        <div className="form-group mb-3">
                            <label htmlFor="exampleInputYear">Year</label>
                            <input 
                                type="text" 
                                name="year" 
                                className="form-control" 
                                id="exampleInputYear"
                                onChange={(e)=>setTakenSubject({...takeSubject, year:e.target.value})}
                            />
                        </div>
                            <input type="submit" name="upload" value="Upload" className="btn btn-primary"/>
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
                                    <button className='btn btn-primary m-2 px-4' onClick={()=>handleUpdate(d.Subject_cord, d.Subject, d.lecture_id, d.year)}>Update</button><br></br>
                                    <button className='btn btn-danger m-2 px-4' onClick={()=>handleDelete(d.Subject_cord)}>Delete</button>
                                </td>
                            </tr>
                        ))}
                        </tbody>
                    </table>
                </div>  
            </div>
        </div>
        <UpdateSubject triggerOpen={trigger} triggerClose={setTrigger} defaultSubject={updateSubject}/>
    </div>
  )
}

export default SubjectSrc

function UpdateSubject(props){
    const [newUpdateSubject, setNewUpdateSubject] = useState({newName:'', newLecture:'', newYear:''});

    useEffect(()=>{
        setNewUpdateSubject({newName:props.defaultSubject[1], newLecture:props.defaultSubject[2], newYear:props.defaultSubject[3]});
    },[props.defaultSubject])

    function handleUpdate(e){
        console.log(newUpdateSubject);
        e.preventDefault();
        axios.put('http://localhost:8081/practical_subject/'+props.defaultSubject[0], {...newUpdateSubject})
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
    }
    return(props.triggerOpen)?(
        <div style={popupStyle.main}>
            <div>
                <button onClick={()=>props.triggerClose(false)}>Close</button>
                <h1>{props.defaultSubject[0]}</h1>
                <div style={{width:"400px", height:"auto"}} className="text-light">
                        <form className=" bg-dark p-4 d-flex flex-column rounded-5" onSubmit={handleUpdate}>
                            <div className="form-group mb-3">
                                <label htmlFor="newInputName">Admin Name</label>
                                <input
                                    type="text"
                                    name="newName"
                                    className="form-control"
                                    id="newInputName"
                                    defaultValue={props.defaultSubject[1]}
                                    onChange={(e)=>setNewUpdateSubject({...newUpdateSubject, newName:e.target.value})}
                                />
                            </div>
                            <div className="form-group mb-3">
                                <label htmlFor="newInputLecture">Admin Name</label>
                                <input
                                    type="text"
                                    name="newLecture"
                                    className="form-control"
                                    id="newInputLecture"
                                    defaultValue={props.defaultSubject[2]}
                                    onChange={(e)=>setNewUpdateSubject({...newUpdateSubject, newLecture:e.target.value})}
                                />
                            </div>
                            <div className="form-group mb-3">
                                <label htmlFor="newInputYear">Admin Name</label>
                                <input
                                    type="text"
                                    name="newYear"
                                    className="form-control"
                                    id="newInputYear"
                                    defaultValue={props.defaultSubject[3]}
                                    onChange={(e)=>setNewUpdateSubject({...newUpdateSubject, newYear:e.target.value})}
                                />
                            </div>
                            <input type="submit" name="upload" value="Upload" className="btn btn-primary"/>
                        </form>

                </div>
            </div>
        </div>
    ): "";
}