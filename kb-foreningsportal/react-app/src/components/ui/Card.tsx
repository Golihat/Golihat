import React from 'react';

type CardProps = {
  title?: string;
  actions?: React.ReactNode;
  children: React.ReactNode;
};

const Card: React.FC<CardProps> = ({ title, actions, children }) => {
  return (
    <section className="kb-card">
      {(title || actions) && (
        <header className="kb-card__header">
          {title && <h3>{title}</h3>}
          {actions && <div className="kb-card__actions">{actions}</div>}
        </header>
      )}
      <div className="kb-card__body">{children}</div>
    </section>
  );
};

export default Card;
