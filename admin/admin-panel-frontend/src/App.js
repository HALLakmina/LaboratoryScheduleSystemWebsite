import './App.css';
import { Routes, Route} from 'react-router-dom'
// import AdminLogin from './admin_screens/AdminLogin';
// import SideNavBar from './admin_screens/SideNavBar';
import "./../node_modules/bootstrap/dist/css/bootstrap.min.css"
import FooterBar from './admin_screens/FooterBar';
import HomeSrc from './admin_screens/HomeSrc';
// import SideNavBar from './admin_screens/SideNavBar';
import AdminLogin from './admin_screens/AdminLogin';
import AdminSrc from './admin_screens/AdminSrc';
import LectureSrc from './admin_screens/LectureSrc';
import SubjectSrc from './admin_screens/SubjectSrc';
import TimetableSrc from './admin_screens/TimetableSrc';
import NewsSrc from './admin_screens/NewsSrc';

function App() {
  
  const backgroundStyle={
    backgroundImage:`url(/img/website_image/Wallpaper.jpg)`,
    backgroundPosition:"center", 
    backgroundAttachment:"fixed", 
    backgroundSize:"cover", 
    backgroundRepeat:"no-repeat",
    miniHeight:"100hv",
    miniWidth:"100wv"
  }
  return (
      <div style={backgroundStyle}>
        <Routes>
          <Route path="/" element={<AdminLogin/>}/>
          <Route path="/HomeSrc" element={<HomeSrc/>}/>
          <Route path="/AdminSrc"element={<AdminSrc/>}/>
          <Route path="/LectureSrc"element={<LectureSrc/>}/>
          <Route path="/SubjectSrc"element={<SubjectSrc/>}/>
          <Route path="/TimetableSrc"element={<TimetableSrc/>}/>
          <Route path="/NewsSrc"element={<NewsSrc/>}/>
        </Routes>
        <FooterBar />
      </div>
  );
}

export default App;
