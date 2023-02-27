import React from 'react';
import { BrowserRouter, Route, Routes } from 'react-router-dom';
import Login from './pages/Login';
import { Footer } from './components/index';


function App(){
  return (
    <React.Fragment>
      <div className="container">
        <BrowserRouter>
          {/* <NavBar /> */}

          <Routes>
            <Route path="/" element={<Login />} />
          </Routes>

          <Footer />
        </BrowserRouter>
      </div>
    </React.Fragment>
  )
}

export default App;
