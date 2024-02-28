import React, { useEffect, useState } from "react";
import SideNavBar from "./SideNavBar";
import axios from "axios";
//import axios from "axios";
function NewsSrc() {
    const [newsData, setNewsData] = useState([]);
    const [takenNews, setTakenNews] = useState({title:'',description:'',location:'',time:'',date:''});
    const [image, setImage] = useState('');
    const [updateNews, setUpdateNews] = useState([]);
    const [trigger, setTrigger] = useState(false);
    useEffect(()=>{
        fetch('http://localhost:8081/news')
        .then(res => res.json())
        .then(data => setNewsData(data))
        .catch(err=> console.error(err));   
    }, [])

    function handleSubmit(e){
        const imageName= image.name;
        e.preventDefault();
        axios.post('http://localhost:8081/add_news', {...takenNews, imageName})
        .then(res=>{
            console.log(res);
            window.location.reload();
        }).catch(err=>console.error(err));
    }

    function handleDelete(id){
        axios.delete('http://localhost:8081/remove_news/'+id)
        .then(res=>{
            window.location.reload();
        })
    }

    function handleUpdate(id, title, image, description, location, data, time){
        setUpdateNews([id, title, image, description, location, data, time]);
        setTrigger(true);
    }

  return (
    <div className="d-flex row-1" style={{minHeight:"100vh"}}>
        <div className="d-flex col-2" style={{position: 'fixed'}}>
            <SideNavBar />
        </div>
        <div className="col-12 d-flex justify-content-end">
            <div className="d-flex col-10  align-items-center flex-column mt-5 text-light mb-5">
            <div style={{width:"400px", height:"auto"}} className="text-light">
                <form className=" bg-dark p-4 d-flex flex-column rounded-5" onSubmit={handleSubmit}>
                    <div className="form-group mb-3">
                        <label htmlFor="exampleInputTitle">Title</label>
                        <input
                            type="text"
                            name="title"
                            className="form-control"
                            id="exampleInputTitle"
                            onChange={(e)=>setTakenNews({...takenNews, title: e.target.value})}
                        />
                    </div>
                    <div className="form-group mb-3">
                        <label htmlFor="exampleInputImage">Image</label>
                        <input
                            type="file"
                            name="image"
                            className="form-control"
                            id="exampleInputImage"
                            onChange={(e)=>setImage(e.target.files[0])}
                        />
                    </div>
                    <div className="form-group mb-3">
                        <label htmlFor="exampleInputDescription">Description</label>
                        <textarea
                            rows="6"
                            name="description"
                            className="form-control"
                            id="exampleInputDescription"
                            onChange={(e)=>setTakenNews({...takenNews, description: e.target.value})}
                        />
                    </div>
                    <div className="form-group mb-3">
                        <label htmlFor="exampleInputLocation">Location</label>
                        <input
                            type="text"
                            name="location"
                            className="form-control"
                            id="exampleInputLocation"
                            onChange={(e)=>setTakenNews({...takenNews, location: e.target.value})}
                        />
                    </div>
                    <div className="form-group mb-3">
                        <label htmlFor="exampleInputTime">Time</label>
                        <input
                            type="time"
                            name="time"
                            className="form-control"
                            id="exampleInputTime"
                            onChange={(e)=>setTakenNews({...takenNews, time: e.target.value})}
                        />
                    </div>
                    <div className="form-group mb-3">
                        <label htmlFor="exampleInputDate">Date</label>
                        <input
                            type="date"
                            name="date"
                            className="form-control"
                            id="exampleInputDate"
                            onChange={(e)=>setTakenNews({...takenNews, date: e.target.value})}
                        />
                    </div>
                        <input
                            type="submit"
                            name="upload"
                            value="Upload"
                            className="btn btn-primary"
                        />
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
                            <button className='btn btn-primary m-2 px-4'onClick={()=>handleUpdate(d.id, d.title, d.image, d.description, d.place, d.time, d.date)}>Update</button><br></br>
                            <button className='btn btn-danger m-2 px-4'onClick={()=>handleDelete(d.id)}>Delete</button>
                        </td>
                    </tr>
                    ))}
                    </tbody>
                </table>
            </div>
            </div>
        </div>
        <UpdateNews triggerOpen={trigger} triggerClose={setTrigger} defaultNews={updateNews}/>
    </div>
  );
}

export default NewsSrc;


function UpdateNews(props){
    const [newUpdateNews, setNewUpdateNews] = useState({title:'', description: '', location: '', date: '',time:''});
    const [newImage, setNewImage] = useState('');

    useEffect(()=>{
        setNewUpdateNews({title:props.defaultNews[1], description:props.defaultNews[3], location:props.defaultNews[4], date:props.defaultNews[6], time:props.defaultNews[5]})
    },[props.defaultNews])

    function handleUpdate(e){
        const newImageName= newImage.name;
        e.preventDefault();
        axios.put('http://localhost:8081/update_news/'+props.defaultNews[0], {...newUpdateNews, newImageName})
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
            <div style={{overflow:"scroll"}}>
                <button onClick={()=>props.triggerClose(false)}>Close</button>
                <h1>{props.defaultNews[0]}</h1>
                <div style={{width:"400px", height:"auto"}} className="text-light">
                    <form className=" bg-dark p-4 d-flex flex-column rounded-5" onSubmit={handleUpdate}>
                        <div className="form-group mb-2">
                            <label htmlFor="newInputTitle">Title</label>
                            <input
                                type="text"
                                name="title"
                                className="form-control"
                                id="newInputTitle"
                                defaultValue={props.defaultNews[1]}
                                onChange={(e)=>setNewUpdateNews({...newUpdateNews, title:e.target.value})}
                            />
                        </div>
                        <div className="form-group mb-2">
                            <label htmlFor="newInputImage">Image</label>
                            <input
                                type="file"
                                name="image"
                                className="form-control"
                                id="newInputImage"
                                filename={props.defaultNews[2]}
                                onChange={(e)=>setNewImage(e.target.files[0])}
                            />
                        </div>
                        <div className="form-group mb-2">
                            <label htmlFor="newInputDescription">Description</label>
                            <textarea
                                rows="4"
                                name="description"
                                className="form-control"
                                id="newInputDescription"
                                defaultValue={props.defaultNews[3]}
                                onChange={(e)=>setNewUpdateNews({...newUpdateNews, description:e.target.value})}
                            />
                        </div>
                        <div className="form-group mb-2">
                            <label htmlFor="newInputLocation">Location</label>
                            <input
                                type="text"
                                name="location"
                                className="form-control"
                                id="newInputLocation"
                                defaultValue={props.defaultNews[4]}
                                onChange={(e)=>setNewUpdateNews({...newUpdateNews, location:e.target.value})}
                            />
                        </div>
                        <div className="form-group mb-2">
                            <label htmlFor="newInputDate">Date</label>
                            <input
                                type="date"
                                name="date"
                                className="form-control"
                                id="newInputDate"
                                defaultValue={props.defaultNews[6]}
                                onChange={(e)=>setNewUpdateNews({...newUpdateNews, date:e.target.value})}
                            />
                        </div>
                        <div className="form-group mb-2">
                            <label htmlFor="newInputTime">Time</label>
                            <input
                                type="time"
                                name="time"
                                className="form-control"
                                id="newInputTime"
                                defaultValue={props.defaultNews[5]}
                                onChange={(e)=>setNewUpdateNews({...newUpdateNews, time:e.target.value})}
                            />
                        </div>
                        <input type="submit" name="upload" value="UPLOAD" className="btn btn-primary"/>
                    </form>
                 </div>
            </div>
        </div>
    ): "";
}