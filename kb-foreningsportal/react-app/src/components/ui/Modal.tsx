import React from 'react';
import Button from './Button';

type ModalProps = {
  title: string;
  open: boolean;
  onClose: () => void;
  children: React.ReactNode;
};

const Modal: React.FC<ModalProps> = ({ title, open, onClose, children }) => {
  if (!open) return null;

  return (
    <div className="kb-modal__overlay" role="dialog" aria-modal="true" aria-label={title}>
      <div className="kb-modal">
        <header className="kb-modal__header">
          <h3>{title}</h3>
          <Button variant="ghost" onClick={onClose} aria-label="Stäng">
            ✕
          </Button>
        </header>
        <div className="kb-modal__body">{children}</div>
      </div>
    </div>
  );
};

export default Modal;
