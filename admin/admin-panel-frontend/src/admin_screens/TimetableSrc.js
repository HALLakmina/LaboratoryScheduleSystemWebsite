/* eslint-disable array-callback-return */
/* eslint-disable no-lone-blocks */
import React, { useEffect, useState } from 'react';
import SideNavBar from './SideNavBar';
import axios from 'axios';
function TimetableSrc() {

  const [timetableData, setTimetableData] = useState([]);
  const [allDetailsTimetable, setAllDetailsTimetableData] = useState([]);
  const [showTimetableData, setShowTimetableData] = useState([])
  const [trigger, setTrigger] = useState(false);
  const [cell, setCell] = useState([]);
  const [allId, setAllId] = useState([]);

  useEffect(()=>{
    fetch('http://localhost:8081/timetable')
        .then(res => res.json())
        .then(data => setTimetableData(data))
        .catch(err=> console.error(err));

    fetch('http://localhost:8081/timetable/allDetails')
        .then(res => res.json())
        .then(data => setAllDetailsTimetableData(data))
        .catch(err=> console.error(err));
  },[])

  useEffect(()=>{
    if(allDetailsTimetable.length !== allId.length){
      allDetailsTimetable.map((data, index)=>{
      setAllId(allId=>[...allId, data.id]);
      })
    }
    //console.log(allDetailsTimetable);
    if(timetableData.length !== allDetailsTimetable.length)
    {
      for(let i=0; i !== allId.length; i++) {
       timetableData.map((data, index)=>{
        if(data.id === allId[i]){
          timetableData.splice(index, 1)
          timetableData.push(allDetailsTimetable[i]);
          return true;
        }
       })
      }
      timetableData.sort((a,b)=>{return a.id - b.id});
      setShowTimetableData(timetableData);
    }
  },[allDetailsTimetable, allId, timetableData])
  const timePeriod = [
    "08.30 AM-09.30 AM",
    "09.30 AM-10.30 AM",
    "10.30 AM-11.30 AM",
    "11.30 AM-12.30 AM",
    "13.00 AM-15.00 AM",
    "15.00 AM-17.00 AM"
  ];
  function edit(id, cord, group, action){
    setCell([id, cord, group, action])
    setTrigger(true);
  }
  return (
    <div className="d-flex row-1 app" style={{minHeight:"100vh"}}>
      <div className="d-flex col-2" style={{position: 'fixed'}}>
        <SideNavBar/>
      </div>
      <div className="col-11 d-flex justify-content-end">
        <div className="d-flex col-10  align-items-center flex-column mt-5 text-light mb-5">
        <div className="my-5">
                    <table className="table table-hover" style={{width:"80vw"}}>
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

                            {timePeriod.map((data, index)=>(
                              <tr key={index}>
                                <td className="px-3 text-center">{data}</td>
                                {showTimetableData.filter((data, indexRow)=>{
                                  
                                  if(index === 0){
                                    if(indexRow === 0 || indexRow === 6 || indexRow === 12 || indexRow === 18 || indexRow === 24){return true}
                                  }
                                  if(index === 1)
                                  {
                                    if(indexRow === 1 || indexRow === 7 || indexRow === 13 || indexRow === 19 || indexRow === 25){return true}
                                  }
                                  if(index === 2)
                                  {
                                    if(indexRow === 2 || indexRow === 8 || indexRow === 14 || indexRow === 20 || indexRow === 26){return true}
                                  }
                                  if(index === 3)
                                  {
                                    if(indexRow === 3 || indexRow === 9 || indexRow === 15 || indexRow === 21 || indexRow === 27){return true}
                                  }
                                  if(index === 4)
                                  {
                                    if(indexRow === 4 || indexRow === 10 || indexRow === 16 || indexRow === 22 || indexRow === 28){return true}
                                  }
                                  if(index === 5)
                                  {
                                    if(indexRow === 5 || indexRow === 11 || indexRow === 17 || indexRow === 23 || indexRow === 29){return true}
                                  }
                                }).map((index)=>(
                                <td className="px-3 ">
                                  <div className="row-1 d-flex justify-content-space-between">
                                    <div className="col-6 d-flex"style={{flexDirection:"column", alignItems:"start"}}>
                                      <p>{index.Subject_cord}<br/>{index.Subject}</p>
                                      <p>{index.year}<br/>{index.practical_group}</p>
                                      <p>{index.full_name}</p>
                                    </div>
                                    <div className="col-4"style={{display:"flex", justifyContent: 'end'}}>
                                      <div className="d-flex "style={{flexDirection:"column", alignItems: 'center'}}>
                                        <div className="text-center" style={{backgroundColor:"purple", color:"white", fontWeight:"bold", fontSize:"10px",  borderRadius:"10px", maxWidth:"40px", paddingLeft:'5px', paddingRight:'5px'}}>
                                          {index.Action}
                                        </div>
                                        <button className="mt-5 btn btn-primary" onClick={()=>edit(index.id, index.Subject_cord, index.practical_group, index.Action)}>Add Request</button>
                                      </div>
                                    </div>
                                    <div className="col-2">
                                    </div>
                                  </div>
                                </td>
                                ))}
                              </tr>
                            ))}
                        </tbody>
                    </table>
                </div>  
        </div>
      </div>
      <TimetableRequestFrom triggerOpen={trigger} triggerClose={setTrigger} requestCell={cell}/>
    </div>
  )
  
}

export default TimetableSrc

function TimetableRequestFrom(props){
  const [newTimetable, setNewTimetable] = useState({newSubjectCord:'', newGroup:'', newActin:''})
  const [subjectCords, setSubjectCords] = useState([]);

  useEffect(()=>{
    fetch('http://localhost:8081/practical_subject')
      .then(res => res.json())
      .then(data => setSubjectCords(data))
      .catch(err=> console.error(err));
  })
  useEffect(()=>{
    setNewTimetable({newSubjectCord:props.requestCell[1], newGroup:props.requestCell[2], newActin:props.requestCell[3]})
  },[props.requestCell])
  function handleUpdate(e){
    e.preventDefault();
    axios.put('http://localhost:8081/timetable/'+props.requestCell[0], {...newTimetable})
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
            <h1>{props.requestCell[0]}</h1>

            <div style={{width:"400px", height:"auto"}} className="text-light">
              <form className=" bg-dark p-4 d-flex flex-column rounded-5" onSubmit={handleUpdate}>
                <div className="form-group mb-3">
                    <label htmlFor="newInputName">Subject cord</label>
                    <select defaultValue={props.requestCell[1]} onChange={(e)=>setNewTimetable({...newTimetable, newSubjectCord:e.target.value})} className="form-select">
                      <option value={''}>---Select Subject Code---</option>
                      {subjectCords.map((data, index)=>(
                        <option value={data.Subject_cord}>{data.Subject_cord}</option>
                        ))}
                    </select>
                </div>

                <div className="form-group mb-3">
                    <label htmlFor="newInputName">Group</label>
                    <select defaultValue={props.requestCell[2]} onChange={(e)=>setNewTimetable({...newTimetable, newGroup:e.target.value})} className="form-select">
                      <option value={''}>---Group---</option>
                      <option>Group01</option>
                      <option>Group02</option>
                    </select>
                </div>

                <div className="form-group mb-3">
                    <label htmlFor="newInputName">Action</label>
                    <select defaultValue={props.requestCell[3]}  onChange={(e)=>setNewTimetable({...newTimetable, newActin:e.target.value})} className="form-select">
                      <option value={"free"}>---Action---</option>
                      <option>active</option>
                      <option>free</option>
                      <option>cancel</option>
                    </select>
                </div>
                <input type="submit" name="upload" value="UPLOAD" className="btn btn-primary"/>
              </form>
            </div>
        </div>
      </div>
    </div>
  ):"";
}