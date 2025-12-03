import React from 'react';
import Card from '../ui/Card';
import Button from '../ui/Button';

type QRCardProps = {
  link: string;
  code: string;
};

const QRCard: React.FC<QRCardProps> = ({ link, code }) => {
  return (
    <Card title="Föreningslänk och QR">
      <p><strong>Länk:</strong> {link}</p>
      <p><strong>Kod:</strong> {code}</p>
      <div className="kb-qr__actions">
        <Button variant="secondary">Kopiera länk</Button>
        <Button variant="ghost">Ladda ned QR</Button>
      </div>
    </Card>
  );
};

export default QRCard;
