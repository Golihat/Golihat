import React from 'react';
import Button from '../ui/Button';

type NavItem = {
  id: string;
  label: string;
};

type NavBarProps = {
  title: string;
  items: NavItem[];
  activeId: string;
  onNavigate: (id: string) => void;
};

const NavBar: React.FC<NavBarProps> = ({ title, items, activeId, onNavigate }) => {
  return (
    <header className="kb-navbar">
      <div className="kb-navbar__brand">{title}</div>
      <nav className="kb-navbar__nav">
        {items.map((item) => (
          <Button
            key={item.id}
            variant={item.id === activeId ? 'primary' : 'ghost'}
            onClick={() => onNavigate(item.id)}
          >
            {item.label}
          </Button>
        ))}
      </nav>
    </header>
  );
};

export default NavBar;
