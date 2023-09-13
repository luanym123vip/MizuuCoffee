import React from 'react';
import "./DetailProduct.scss";
import Header from '../../components/header/Header';
import HeroListProduct from '../Shop/components/Hero/Hero';
import Herowrap from '../Herowrap/Herowrap';
import Footer from '../../components/footer/Footer';
import Detail from './components/Detail/Detail';
const DetailProduct = () => {
    return (
        <div>
            <Header />
            <HeroListProduct />
            <Herowrap title={"Detail Product"} />
            <Detail />
            <Footer />
        </div>
    )
}

export default DetailProduct
