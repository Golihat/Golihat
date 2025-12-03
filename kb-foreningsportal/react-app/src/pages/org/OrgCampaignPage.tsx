import React from 'react';
import Card from '../../components/ui/Card';
import Button from '../../components/ui/Button';

const OrgCampaignPage: React.FC = () => (
  <div className="kb-grid">
    <Card title="Kampanjstatus" actions={<Button variant="secondary">Redigera</Button>}>
      <p>Status: Pågående</p>
      <p>Period: 1 maj – 31 maj</p>
    </Card>
    <Card title="Automatiska utskick">
      <ul className="kb-list">
        <li className="kb-list__item">Startmail skickat</li>
        <li className="kb-list__item">Mitt-i-kampanj utskick planerat</li>
        <li className="kb-list__item">Avslutsmail planerat</li>
      </ul>
    </Card>
  </div>
);

export default OrgCampaignPage;
