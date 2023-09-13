import React from 'react';
import "../../../../css/bootstrap2.min.css";
import "./Herowrap.scss";
import a9969 from "./9969.jpg";
const Herowrap = ({ title }) => {
    return (
        <div className="hero-wrap hero-bread" style={{
            backgroundImage: `url(${a9969})`, backgroundRepeat: 'no-repeat', backgroundSize: 'cover',
            overflow: 'hidden', backgroundPosition: 'center ',
        }}>
            <div className="container">
                <div className="row no-gutters slider-text align-items-center justify-content-center">
                    <div className="col-md-9 ftco-animate text-center">
                        <p className="breadcrumbs"><span className="mr-2"><a href="/home">Home</a></span> <span>{title}</span></p>
                        <h1 className="mb-0 bread">{title}</h1>
                    </div>
                </div>
            </div>
        </div >
    )
}

export default Herowrap
