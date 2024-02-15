import React from 'react'
import SideNavBar from './SideNavBar'

function AdminSrc() {
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
                            <label for="exampleInputAdminID">Admin ID</label>
                            <input type="text" name="adminId" class="form-control" id="exampleInputAdminID"></input>
                        </div>
                        <div className="form-group mb-3">
                            <label for="exampleInputAdminName">Admin Name</label>
                            <input type="text" name="adminName" class="form-control" id="exampleInputAdminName"></input>
                        </div>
                        <div className="form-group mb-3">
                            <label for="exampleInputUserName">User Name</label>
                            <input type="text" name="userName" class="form-control" id="exampleInputUserName"></input>
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
                        <tr>
                            <td className="px-3 text-center">111</td>
                            <td className="px-3 text-center">Lahiru</td>
                            <td className="px-3 text-center">HALLakmina</td>
                            <td className="px-3 text-center">lahirulakmina@gmail.com</td>
                            <td className="px-3 text-center">0713245764</td>
                            <td className="px-3 text-center">kamalD</td>
                            <td className="px-3 d-flex justify-content-center">
                                <button className='btn btn-primary m-2 px-4'>Update</button><br></br>
                                <button className='btn btn-danger m-2 px-4'>Delete</button>
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

export default AdminSrc