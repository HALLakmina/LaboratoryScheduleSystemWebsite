import { useEffect, useState } from "react";
import React from 'react'
import axios from 'axios';
import { useParams } from "react-router-dom";

function AdminUpdate() {

const {id} = useParams
    const [allValues, setAllValues] = useState(
        {
            adminName:'',
            userName:'',
            email:'',
            mobileNo:'',
            password:'',
        }
    );

    // useEffect(()=>{
    //     axios.get('http://localhost:8081/search_admin_details/'+id)
    //     .then(res=>{
    //         setAllValues({...allValues, adminName: res.data[0].AdminName, userName: res.data[0].UserName,
    //         email: res.data[0].Email, mobileNo: res.data[0].Mobile, password: res.data[0].Password});
    //     })
    // })

    function handleUpdate (e){
        e.preventDefault();
        axios.put('http://localhost:8081/update_admin_details/'+id, allValues)
//             .then(res=>{
//             console.log(res);
//             //window.location.reload();
//             //navigate('/AdminSrc')
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
    return (
        <div style={popupStyle.main}>
            <div>
                <h1>{id}</h1>
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
                                value={allValues.adminName}
                                onChange={(e)=>setAllValues({...allValues, adminName: e.target.value})}
                            />
                        </div>
                        <div className="form-group mb-3">
                            <label for="exampleInputUserName">User Name</label>
                            <input
                                type="text"
                                name="userName"
                                class="form-control"
                                id="exampleInputUserName"
                                onChange={(e)=>setAllValues({...allValues, userName: e.target.value})}
                            />
                        </div>
                        <div className="form-group mb-3">
                            <label for="exampleInputEmail">Email</label>
                            <input
                                type="text"
                                name="email"
                                class="form-control"
                                id="exampleInputEmail"
                                onChange={(e)=>setAllValues({...allValues, email: e.target.value})}
                            />
                        </div>
                        <div className="form-group mb-3">
                            <label for="exampleInputMobileNo">Mobil No</label>
                            <input
                                type="number"
                                name="mobileNo"
                                class="form-control"
                                id="exampleInputMobileNo"
                                onChange={(e)=>setAllValues({...allValues, mobileNo: e.target.value})}
                            />
                        </div>
                        <div className="form-group mb-3">
                            <label for="exampleInputPassword">Password</label>
                            <input
                                type="password"
                                name="password"
                                class="form-control"
                                id="exampleInputPassword"
                                onChange={(e)=>setAllValues({...allValues, password: e.target.value})}
                            />
                        </div>
                            <input type="submit" name="upload" value="Upload" class="btn btn-primary"></input>
                    </form>
                        
                </div>
            </div>
        </div>
    )
}
export default AdminUpdate;