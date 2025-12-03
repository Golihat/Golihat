import React from 'react';
import QRCard from '../../components/qr/QRCard';
import Card from '../../components/ui/Card';

const AdminOrgDetailPage: React.FC = () => (
  <div className="kb-grid">
    <QRCard link="https://kryddbox.se/forening/if-bla" code="IFBLA2024" />
    <Card title="Kampanj" actions={<a href="#">Visa som förening</a>}>
      <p>Kampanjstatus: Pågående</p>
      <p>Datum: 1 maj – 31 maj</p>
    </Card>
    <Card title="Material">
      <ul className="kb-list">
        <li className="kb-list__item">Affischer (PDF)</li>
        <li className="kb-list__item">Flyer (PDF)</li>
        <li className="kb-list__item">SoMe-kit</li>
      </ul>
    </Card>
  </div>
);

export default AdminOrgDetailPage;
