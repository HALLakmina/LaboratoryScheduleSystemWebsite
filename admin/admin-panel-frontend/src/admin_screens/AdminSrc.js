import React from 'react'
import { useEffect, useState } from 'react'
import SideNavBar from './SideNavBar'
import axios from 'axios';
import { useNavigate } from 'react-router-dom';

function AdminSrc() {
    const [adminData, setAdminData] = useState([]);
    useEffect(()=>{
        fetch('http://localhost:8081/admin_details')
        .then(res => res.json())
        .then(adminData => setAdminData(adminData))
        .catch(err => console.error(err));
    },[])

    const [adminID, setAdminID] = useState('');
    const [adminName, setAdminName] = useState('');
    const [userName, setUserName] = useState('');
    const [email, setEmail] = useState('');
    const [mobileNo, setMobileNo] = useState(0);
    const [password, setPassword] = useState('');
    const [trigger, setTrigger] = useState(false);
    const [userId, setUserId] = useState('');
    const [userData, setUserData] = useState([]);

    const navigate = useNavigate();

    function handleSubmit(e){
        e.preventDefault();
        axios.post('http://localhost:8081/add_admin_details', { adminName, userName, adminID, mobileNo, email, password})
        .then(res=>{
            console.log(res);
            window.location.reload();
            navigate('/AdminSrc')
        }).catch(err=>console.log(err));
        console.log(adminID, adminName, userName, email, mobileNo, password);
    }
    const handleDelete = (id) =>{
        axios.delete('http://localhost:8081/remove_admin_details/'+id)
       .then(res => {
            window.location.reload()
       })
    }

    const handleUpdate= (id, name, userName, email, mobileNo, password) =>{
        const userArray = [name, userName, email, mobileNo, password];
        setTrigger(true);
        setUserId(id);
        setUserData(userArray)
    }
  return (
    <div className="d-flex row-1" style={{minHeight:"100vh"}}> 
        <div className="d-flex col-2" style={{position: 'fixed'}}>
            <SideNavBar/>
        </div>
        <div className="col-12 d-flex justify-content-end">
            <div className="d-flex col-10  align-items-center flex-column mt-5 text-light mb-5">
                <div style={{width:"400px", height:"auto"}} className="text-light">
                    <form className=" bg-dark p-4 d-flex flex-column rounded-5" onSubmit={handleSubmit}>
                        <div className="form-group mb-3">
                            <label for="exampleInputAdminID">Admin ID</label>
                            <input
                                type="text"
                                name="adminId"
                                class="form-control"
                                id="exampleInputAdminID"
                                onChange={(e)=>setAdminID(e.target.value)}
                            />
                        </div>
                        <div className="form-group mb-3">
                            <label for="exampleInputAdminName">Admin Name</label>
                            <input
                                type="text"
                                name="adminName"
                                class="form-control"
                                id="exampleInputAdminName"
                                onChange={(e)=>setAdminName(e.target.value)}    
                            />
                        </div>
                        <div className="form-group mb-3">
                            <label for="exampleInputUserName">User Name</label>
                            <input
                                type="text"
                                name="userName"
                                class="form-control"
                                id="exampleInputUserName"
                                onChange={(e)=>setUserName(e.target.value)}
                            />
                        </div>
                        <div className="form-group mb-3">
                            <label for="exampleInputEmail">Email</label>
                            <input
                                type="text"
                                name="email"
                                class="form-control"
                                id="exampleInputEmail"
                                onChange={(e)=>setEmail(e.target.value)}
                            />
                        </div>
                        <div className="form-group mb-3">
                            <label for="exampleInputMobileNo">Mobil No</label>
                            <input
                                type="number"
                                name="mobileNo"
                                class="form-control"
                                id="exampleInputMobileNo"
                                onChange={(e)=>setMobileNo(e.target.value)}  
                            />
                        </div>
                        <div className="form-group mb-3">
                            <label for="exampleInputPassword">Password</label>
                            <input
                                type="password"
                                name="password"
                                class="form-control"
                                id="exampleInputPassword"
                                onChange={(e)=>setPassword(e.target.value)}
                            />
                        </div>
                            <input type="submit" name="upload" value="Upload" class="btn btn-primary"></input>
                    </form>
                        
                </div>
                <div className="my-5">
                    <table className="table table-hover">
                        <thead className="thead-dark">
                            <tr>
                                <th className="px-3 text-center">Admin ID</th>
                                <th className="px-3 text-center">Admin Name</th>
                                <th className="px-3 text-center">User Name</th>
                                <th className="px-3 text-center">Email</th>
                                <th className="px-3 text-center">Mobil No</th>
                                <th className="px-3 text-center">Password</th>
                                <th className="px-3 text-center">Edit</th>
                            </tr>
                        </thead>
                        <tbody>
                        {adminData.map((d, i) =>(
                        <tr key={i}>
                            <td className="px-3 text-center">{d.nic}</td>
                            <td className="px-3 text-center">{d.full_name}</td>
                            <td className="px-3 text-center">{d.user_name}</td>
                            <td className="px-3 text-center">{d.email}</td>
                            <td className="px-3 text-center">{d.mobile_number}</td>
                            <td className="px-3 text-center">{d.password}</td>
                            <td className="px-3 d-flex justify-content-center">
                                <button className='btn btn-primary m-2 px-4' onClick={()=>handleUpdate(d.nic, d.full_name, d.user_name, d.email, d.mobile_number, d.password)}>Update</button><br></br>
                                <button className='btn btn-danger m-2 px-4' onClick={()=>handleDelete(d.nic)}>Delete</button>
                            </td>
                        </tr>
                        ))}
                        </tbody>
                    </table>
                </div>
            </div>
            <Popup triggerOpen={trigger} triggerClose={setTrigger} takeUserID={userId} userInformation={userData}/>
            {/* <AdminUpdate trigger={OpenUpdate} setTrigger={setOpenUpdate} takeUserID={userID} defaultData={allDate}/> */}
        </div>
        
    </div>
  )
}

function Popup(props){
    //const [updateAdminID, setUpdateAdminID] = useState('');
    let [updateAdminName, setUpdateAdminName] = useState('');
    let [updateUserName, setUpdateUserName] = useState(props.userInformation[1]);
    let [updateEmail, setUpdateEmail] = useState(props.userInformation[2]);
    let [updateMobileNo, setUpdateMobileNo] = useState(props.userInformation[3]);
    let [updatePassword, setUpdatePassword] = useState(props.userInformation[4]);
    let [check, setCheck] = useState(props.userInformation[0])

    function handleUpdate(e){
        if(updateAdminName==="")
        {
            console.log("01",check);
            // console.log(updateAdminName);
            // console.log("Yes null");
            // setUpdateAdminName(props.userInformation[0]);
            // console.log(updateAdminName);
            setCheck(props.userInformation[3]);
            console.log("02",check);
        }
        else
        {
            console.log("Not null");
        }
        e.preventDefault();
        //setUpdateAdminName(props.defaultData[0]);
        //console.log("runStart", ' name=> ',updateAdminName,'email=>',updateEmail);
        console.log('03',check);
        axios.put('http://localhost:8081/update_admin_details/'+props.takeUserID, {updateAdminName, updateUserName, updateEmail, updateMobileNo, updatePassword})
            .then(res=>{
            console.log(res);
            //window.location.reload();
            //navigate('/AdminSrc')
        }).catch(err=>console.log(err));
        console.log(updateAdminName,updateUserName,updateEmail,updateMobileNo,updatePassword);
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
    return (props.triggerOpen) ? (
        <div style={popupStyle.main}>
            <div>
                <button onClick={()=>props.triggerClose(false)}>close</button>
                <h1>{props.takeUserID}</h1>
                <div style={{width:"400px", height:"auto"}} className="text-light">
                    <form className=" bg-dark p-4 d-flex flex-column rounded-5" onSubmit={handleUpdate}>
                        <div className="form-group mb-3">
                        {/* <input
                                type="hidden"
                                name="adminID"
                                class="form-control"
                                id="exampleInputAdminName"
                            /> */}
                            <label for="exampleInputAdminName">Admin Name</label>
                            <input
                                type="text"
                                name="adminName"
                                class="form-control"
                                id="exampleInputAdminName"
                                defaultValue={props.userInformation[0]}
                                onChange={(e)=>setUpdateAdminName(e.target.value)}
                            />
                        </div>
                        <div className="form-group mb-3">
                            <label for="exampleInputUserName">User Name</label>
                            <input
                                type="text"
                                name="userName"
                                class="form-control"
                                id="exampleInputUserName"
                                defaultValue={props.userInformation[1]}
                                onChange={(e)=>setUpdateUserName(e.target.value)}
                            />
                        </div>
                        <div className="form-group mb-3">
                            <label for="exampleInputEmail">Email</label>
                            <input
                                type="text"
                                name="email"
                                class="form-control"
                                id="exampleInputEmail"
                                defaultValue={props.userInformation[2]}
                                onChange={(e)=>setUpdateEmail(e.target.value)}
                            />
                        </div>
                        <div className="form-group mb-3">
                            <label for="exampleInputMobileNo">Mobil No</label>
                            <input
                                type="number"
                                name="mobileNo"
                                class="form-control"
                                id="exampleInputMobileNo"
                                defaultValue={props.userInformation[3]}
                                onChange={(e)=>setUpdateMobileNo(e.target.value)}
                            />
                        </div>
                        <div className="form-group mb-3">
                            <label for="exampleInputPassword">Password</label>
                            <input
                                type="password"
                                name="password"
                                class="form-control"
                                id="exampleInputPassword"
                                defaultValue={props.userInformation[4]}
                                onChange={(e)=>setUpdatePassword(e.target.value)}
                            />
                        </div>
                            <input type="submit" name="upload" value="Upload" class="btn btn-primary"></input>
                    </form>
                        
                </div>
            </div>
        </div>   
        
    ) : '';
}

export default AdminSrc